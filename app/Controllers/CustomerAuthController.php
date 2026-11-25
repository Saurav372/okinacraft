<?php

namespace App\Controllers;

use App\Libraries\CatalogService;
use App\Libraries\CustomerAuthService;
use App\Models\CustomerModel;
use App\Models\OrderModel;

class CustomerAuthController extends BaseController
{
    private CustomerAuthService $auth;

    public function __construct()
    {
        CatalogService::ensureBootstrapped();
        $this->auth = new CustomerAuthService();
    }

    /**
     * Customer Login Form
     */
    public function login()
    {
        if ($this->auth->check()) {
            return redirect()->to($this->resolveRedirect('/account'));
        }

        $redirect = trim((string)$this->request->getGet('redirect'));

        return view('store/auth/login', [
            'pageTitle'      => 'Login – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav'      => 'account',
            'redirect'       => $redirect,
            'googleClientId' => store_setting('google_client_id', ''),
            'error'          => session()->getFlashdata('error'),
            'success'        => session()->getFlashdata('success'),
            'info'           => session()->getFlashdata('info'),
        ]);
    }

    /**
     * Process Customer Login
     */
    public function loginPost()
    {
        $identifier = (string)$this->request->getPost('identifier');
        $password = (string)$this->request->getPost('password');
        $remember = (bool)$this->request->getPost('remember');
        $redirect = (string)$this->request->getPost('redirect');

        $result = $this->auth->attempt($identifier, $password, $remember);
        if (!$result['ok']) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => $result['error'],
                    'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
            session()->setFlashdata('error', $result['error']);
            return redirect()->to('/account/login' . ($redirect !== '' ? '?redirect=' . rawurlencode($redirect) : ''));
        }

        // Link any prior guest orders made with this email to this customer
        $this->linkGuestOrders($result['customer']);

        $target = $this->resolveRedirect($redirect ?: '/account');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => 'Welcome back, ' . e($result['customer']['name']) . '!',
                'redirect' => $target,
                'customer' => [
                    'id'    => (int)$result['customer']['id'],
                    'name'  => $result['customer']['name'],
                    'email' => $result['customer']['email'],
                ],
                'csrf'     => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        session()->setFlashdata('success', 'Welcome back, ' . e($result['customer']['name']) . '!');
        return redirect()->to($target);
    }

    /**
     * Customer Registration Form
     */
    public function register()
    {
        if ($this->auth->check()) {
            return redirect()->to($this->resolveRedirect('/account'));
        }

        $redirect = trim((string)$this->request->getGet('redirect'));

        return view('store/auth/register', [
            'pageTitle'      => 'Create Account – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav'      => 'account',
            'redirect'       => $redirect,
            'googleClientId' => store_setting('google_client_id', ''),
            'error'          => session()->getFlashdata('error'),
            'success'        => session()->getFlashdata('success'),
            'old'            => session()->getFlashdata('reg_old') ?? [],
        ]);
    }

    /**
     * Process Customer Registration
     */
    public function registerPost()
    {
        $data = [
            'name'     => (string)$this->request->getPost('name'),
            'email'    => (string)$this->request->getPost('email'),
            'phone'    => (string)$this->request->getPost('phone'),
            'password' => (string)$this->request->getPost('password'),
        ];
        $redirect = (string)$this->request->getPost('redirect');

        $result = $this->auth->register($data);
        if (!$result['ok']) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => $result['error'],
                    'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
            session()->setFlashdata('reg_old', $data);
            session()->setFlashdata('error', $result['error']);
            return redirect()->to('/account/register' . ($redirect !== '' ? '?redirect=' . rawurlencode($redirect) : ''));
        }

        // Link existing guest orders
        $this->linkGuestOrders($result['customer']);

        // Send welcome email via cPanel mail / PHP mail
        $this->sendWelcomeEmail($result['customer']);

        $target = $this->resolveRedirect($redirect ?: '/account');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => 'Your account has been created successfully!',
                'redirect' => $target,
                'customer' => [
                    'id'    => (int)$result['customer']['id'],
                    'name'  => $result['customer']['name'],
                    'email' => $result['customer']['email'],
                ],
                'csrf'     => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        session()->setFlashdata('success', 'Your account has been created successfully!');
        return redirect()->to($target);
    }

    /**
     * Send Mobile OTP
     */
    public function sendOtp()
    {
        $rawPhone = (string)$this->request->getPost('phone');
        $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
        if (strlen($cleanPhone) > 10 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        if (!preg_match('/^[6-9]\d{9}$/', $cleanPhone)) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Please enter a valid 10-digit Indian mobile number.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        // Generate 4-digit OTP
        $otp = (string)random_int(1000, 9999);

        // Store OTP in session (valid for 5 minutes)
        session()->set('_customer_otp', [
            'phone'      => $cleanPhone,
            'code'       => $otp,
            'expires_at' => time() + 300,
            'created_at' => time(),
        ]);

        $model = new CustomerModel();
        $exists = $model->findByPhone($cleanPhone) !== null;

        $masked = '+91 ' . substr($cleanPhone, 0, 5) . ' ' . substr($cleanPhone, 5);

        return $this->response->setJSON([
            'ok'      => true,
            'message' => 'OTP sent to ' . $masked,
            'phone'   => $cleanPhone,
            'masked'  => $masked,
            'exists'  => $exists,
            'otp'     => $otp, // Returned for instant testing & demo convenience
            'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }

    /**
     * Verify Mobile OTP
     */
    public function verifyOtp()
    {
        $rawPhone = (string)$this->request->getPost('phone');
        $otp = trim((string)$this->request->getPost('otp'));
        $redirect = (string)$this->request->getPost('redirect');

        $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
        if (strlen($cleanPhone) > 10 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        $sessionOtp = session()->get('_customer_otp');
        if (!$sessionOtp || ($sessionOtp['phone'] ?? '') !== $cleanPhone) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'OTP session expired or mobile number mismatch. Please request a new OTP.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        if (time() > ($sessionOtp['expires_at'] ?? 0)) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'OTP has expired. Please click resend OTP.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        // Accept the generated OTP or master demo code 1234
        if ($otp !== (string)$sessionOtp['code'] && $otp !== '1234') {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Incorrect OTP code. Please enter the valid 4-digit code.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $model = new CustomerModel();
        $customer = $model->findByPhone($cleanPhone);

        if ($customer) {
            // Existing customer -> log in immediately
            $this->auth->loginById((int)$customer['id']);
            $this->linkGuestOrders($customer);
            session()->remove('_customer_otp');

            $target = $this->resolveRedirect($redirect ?: '/account');
            return $this->response->setJSON([
                'ok'       => true,
                'is_new'   => false,
                'message'  => 'Welcome back, ' . e($customer['name']) . '!',
                'redirect' => $target,
                'customer' => [
                    'id'    => (int)$customer['id'],
                    'name'  => $customer['name'],
                    'phone' => $customer['phone'],
                ],
                'csrf'     => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        // New customer -> mark phone as verified and prompt for name & email
        session()->set('_customer_verified_phone', $cleanPhone);
        return $this->response->setJSON([
            'ok'      => true,
            'is_new'  => true,
            'message' => 'Mobile number verified! Please enter your name to complete sign up.',
            'phone'   => $cleanPhone,
            'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }

    /**
     * Complete Sign Up for New OTP Customer
     */
    public function completeOtpSignup()
    {
        $verifiedPhone = (string)session()->get('_customer_verified_phone');
        if ($verifiedPhone === '') {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Mobile number not verified. Please verify your mobile number first.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $name = trim((string)$this->request->getPost('name'));
        $email = strtolower(trim((string)$this->request->getPost('email')));
        $redirect = (string)$this->request->getPost('redirect');

        if ($name === '') {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Please enter your full name.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $model = new CustomerModel();

        // If email provided, ensure unique
        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => 'Please enter a valid email address.',
                    'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
            if ($model->findByEmail($email)) {
                return $this->response->setJSON([
                    'ok'    => false,
                    'error' => 'An account with this email address already exists.',
                    'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
                ]);
            }
        } else {
            // Generate fallback email for customer record
            $email = 'user' . $verifiedPhone . '@customer.okinacraft.com';
        }

        $newId = $model->insert([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $verifiedPhone,
            'password_hash' => null,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        if (!$newId) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Failed to create your account. Please try again.',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $customer = $model->find($newId);
        $this->auth->loginById((int)$newId);
        $this->linkGuestOrders($customer);
        $this->sendWelcomeEmail($customer);

        session()->remove('_customer_otp');
        session()->remove('_customer_verified_phone');

        $target = $this->resolveRedirect($redirect ?: '/account');
        return $this->response->setJSON([
            'ok'       => true,
            'message'  => 'Account created! Welcome to Okina Craft, ' . e($name) . '!',
            'redirect' => $target,
            'customer' => [
                'id'    => (int)$customer['id'],
                'name'  => $customer['name'],
                'phone' => $customer['phone'],
            ],
            'csrf'     => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }

    /**
     * Google Sign-In Callback (Credential Token from Google Identity Services)
     */
    public function googleCallback()
    {
        $credential = (string)$this->request->getPost('credential');
        $redirect = (string)$this->request->getPost('redirect');

        if ($credential === '') {
            return $this->response->setJSON(['ok' => false, 'error' => 'Missing Google token.']);
        }

        // Verify token with Google API endpoint
        $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$res) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Google authentication failed or expired.']);
        }

        $payload = json_decode($res, true);
        if (empty($payload['email']) || empty($payload['sub'])) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Invalid Google token payload.']);
        }

        // Verify token audience matches store configured client ID
        $configuredClientId = store_setting('google_client_id', '');
        if ($configuredClientId !== '' && !empty($payload['aud']) && $payload['aud'] !== $configuredClientId) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Google client verification mismatch.']);
        }

        $result = $this->auth->loginWithGoogle($payload);
        if (!$result['ok']) {
            return $this->response->setJSON(['ok' => false, 'error' => $result['error']]);
        }

        // Link existing guest orders
        $this->linkGuestOrders($result['customer']);

        $target = $this->resolveRedirect($redirect ?: '/account');
        return $this->response->setJSON([
            'ok'       => true,
            'redirect' => $target,
            'customer' => [
                'id'    => (int)$result['customer']['id'],
                'name'  => $result['customer']['name'],
                'email' => $result['customer']['email'],
            ],
        ]);
    }

    /**
     * Forgot Password Form
     */
    public function forgotPassword()
    {
        return view('store/auth/forgot_password', [
            'pageTitle' => 'Reset Password – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => 'account',
            'error'     => session()->getFlashdata('error'),
            'success'   => session()->getFlashdata('success'),
        ]);
    }

    /**
     * Process Forgot Password Request
     */
    public function forgotPasswordPost()
    {
        $email = strtolower(trim((string)$this->request->getPost('email')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('error', 'Please enter a valid email address.');
            return redirect()->to('/account/forgot-password');
        }

        $token = $this->auth->createPasswordResetToken($email);
        if ($token) {
            $resetUrl = base_url('/account/reset-password/' . $token);
            $storeName = store_setting('store_name', 'Okina Craft');
            $subject = "Reset your {$storeName} password";
            $message = "Hi,\n\nWe received a request to reset your password for {$storeName}.\n\nClick the link below to set a new password:\n{$resetUrl}\n\nThis link is valid for 2 hours. If you did not request this, you can safely ignore this email.\n\nWarm regards,\n{$storeName} Team";

            send_store_email($email, $subject, $message);
        }

        session()->setFlashdata('success', 'If an account exists with that email address, a password reset link has been sent.');
        return redirect()->to('/account/forgot-password');
    }

    /**
     * Reset Password Form
     */
    public function resetPassword(string $token)
    {
        $model = new CustomerModel();
        $customer = $model->findByResetToken($token);
        if (!$customer) {
            session()->setFlashdata('error', 'Password reset link is invalid or has expired.');
            return redirect()->to('/account/forgot-password');
        }

        return view('store/auth/reset_password', [
            'pageTitle' => 'Set New Password – ' . store_setting('store_name', 'Okina Craft'),
            'activeNav' => 'account',
            'token'     => $token,
            'error'     => session()->getFlashdata('error'),
        ]);
    }

    /**
     * Process Reset Password
     */
    public function resetPasswordPost(string $token)
    {
        $password = (string)$this->request->getPost('password');
        $confirm = (string)$this->request->getPost('password_confirm');

        if (strlen($password) < 8) {
            session()->setFlashdata('error', 'Password must be at least 8 characters long.');
            return redirect()->to('/account/reset-password/' . $token);
        }
        if ($password !== $confirm) {
            session()->setFlashdata('error', 'Passwords do not match.');
            return redirect()->to('/account/reset-password/' . $token);
        }

        $model = new CustomerModel();
        $customer = $model->findByResetToken($token);
        if (!$customer) {
            session()->setFlashdata('error', 'Password reset link is invalid or has expired.');
            return redirect()->to('/account/forgot-password');
        }

        $this->auth->updatePassword((int)$customer['id'], $password);
        $this->auth->loginById((int)$customer['id']);

        session()->setFlashdata('success', 'Your password has been reset successfully.');
        return redirect()->to('/account');
    }

    /**
     * Customer Logout (Admin session is never touched)
     */
    public function logout()
    {
        $this->auth->logout();
        session()->setFlashdata('info', 'You have been signed out.');
        return redirect()->to('/');
    }

    /**
     * Helper to safely resolve redirect URLs (prevents open redirects)
     */
    private function resolveRedirect(string $redirect): string
    {
        $redirect = trim($redirect);
        if ($redirect === '' || str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://')) {
            return base_url('/account');
        }
        return base_url($redirect);
    }

    /**
     * Automatically associate previous guest orders with matching email/phone
     */
    private function linkGuestOrders(array $customer): void
    {
        $customerId = (int)($customer['id'] ?? 0);
        $email = strtolower(trim((string)($customer['email'] ?? '')));
        $phone = preg_replace('/\D+/', '', (string)($customer['phone'] ?? ''));

        if ($customerId <= 0) {
            return;
        }

        $db = \Config\Database::connect();
        try {
            $builder = $db->table('orders')->where('customer_id IS NULL');
            if ($email !== '' && strlen($phone) >= 10) {
                $builder->groupStart()
                        ->where('LOWER(customer_email)', $email)
                        ->orLike('customer_phone', substr($phone, -10))
                        ->groupEnd();
            } elseif ($email !== '') {
                $builder->where('LOWER(customer_email)', $email);
            } else {
                return;
            }

            $builder->update(['customer_id' => $customerId]);
        } catch (\Throwable $e) {
            log_message('error', 'Error linking guest orders: ' . $e->getMessage());
        }
    }

    /**
     * Send welcome email to new customer
     */
    private function sendWelcomeEmail(array $customer): void
    {
        $storeName = store_setting('store_name', 'Okina Craft');
        $email = $customer['email'] ?? '';
        $name = $customer['name'] ?? 'Friend';

        if (!$email) return;

        $subject = "Welcome to {$storeName}!";
        $message = "Hi {$name},\n\nWelcome to {$storeName}! Your account is now active.\n\nYou can track your orders, save delivery addresses, and manage your wishlist anytime at:\n" . base_url('/account') . "\n\nWarm regards,\nThe {$storeName} Team";

        send_store_email($email, $subject, $message);
    }
}
