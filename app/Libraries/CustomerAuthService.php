<?php

namespace App\Libraries;

use App\Models\CustomerModel;

class CustomerAuthService
{
    private function session()
    {
        return \Config\Services::session();
    }

    /**
     * Get currently authenticated customer
     */
    public function customer(): ?array
    {
        $id = $this->session()->get('_customer_id');
        if (!$id) {
            return null;
        }

        $model = new CustomerModel();
        $customer = $model->find((int)$id);
        if (!$customer || ($customer['status'] ?? 'active') !== 'active') {
            $this->session()->remove('_customer_id');
            return null;
        }

        return $customer;
    }

    /**
     * Check if customer is logged in
     */
    public function check(): bool
    {
        return $this->customer() !== null;
    }

    /**
     * Get customer ID
     */
    public function id(): ?int
    {
        $customer = $this->customer();
        return $customer ? (int)$customer['id'] : null;
    }

    /**
     * Login customer by ID directly (e.g. after registration or Google sign-in)
     */
    public function loginById(int $customerId): void
    {
        $this->session()->regenerate(true);
        $this->session()->set('_customer_id', $customerId);
    }

    /**
     * Authenticate via email or phone + password
     */
    public function attempt(string $identifier, string $password): array
    {
        $identifier = trim($identifier);

        // Session-based rate limiting on customer logins
        $attempts = $this->session()->get('_customer_login_attempts') ?? ['count' => 0, 'until' => 0];
        if (time() < ($attempts['until'] ?? 0) && ($attempts['count'] ?? 0) >= 5) {
            $wait = max(1, (int)ceil(($attempts['until'] - time()) / 60));
            return ['ok' => false, 'error' => "Too many failed login attempts. Please try again in {$wait} minute(s)."];
        }

        $model = new CustomerModel();

        // Check if identifier is email or phone number
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $customer = $model->findByEmail($identifier);
        } else {
            $customer = $model->findByPhone($identifier);
        }

        if (!$customer) {
            $this->recordFailedAttempt();
            return ['ok' => false, 'error' => 'No account found matching those credentials.'];
        }

        if (($customer['status'] ?? 'active') !== 'active') {
            return ['ok' => false, 'error' => 'This account has been suspended. Please contact customer support.'];
        }

        // If registered with Google only and has no local password
        if (empty($customer['password_hash'])) {
            return ['ok' => false, 'error' => 'This account was registered using Google. Please sign in with Google or reset your password.'];
        }

        if (!password_verify($password, (string)$customer['password_hash'])) {
            $this->recordFailedAttempt();
            return ['ok' => false, 'error' => 'Incorrect password. Please try again.'];
        }

        $this->session()->remove('_customer_login_attempts');
        $this->loginById((int)$customer['id']);

        return ['ok' => true, 'customer' => $customer];
    }

    /**
     * Authenticate or register customer via Google OAuth
     */
    public function loginWithGoogle(array $googleData): array
    {
        $googleId = trim((string)($googleData['sub'] ?? $googleData['id'] ?? ''));
        $email = strtolower(trim((string)($googleData['email'] ?? '')));
        $name = trim((string)($googleData['name'] ?? 'Google Customer'));
        $avatar = trim((string)($googleData['picture'] ?? ''));

        if ($email === '' || $googleId === '') {
            return ['ok' => false, 'error' => 'Google account data could not be verified.'];
        }

        $model = new CustomerModel();

        // Check if user exists by google_id or email
        $customer = $model->findByGoogleId($googleId) ?: $model->findByEmail($email);

        if ($customer) {
            // Update google_id and avatar if missing
            $updates = [];
            if (empty($customer['google_id'])) {
                $updates['google_id'] = $googleId;
            }
            if (!empty($avatar) && empty($customer['avatar_url'])) {
                $updates['avatar_url'] = $avatar;
            }
            if ($updates) {
                $model->update((int)$customer['id'], $updates);
                $customer = array_merge($customer, $updates);
            }

            $this->loginById((int)$customer['id']);
            return ['ok' => true, 'customer' => $customer, 'is_new' => false];
        }

        // Create new customer account via Google
        $newId = $model->insert([
            'name'          => $name,
            'email'         => $email,
            'google_id'     => $googleId,
            'avatar_url'    => $avatar,
            'password_hash' => null,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        if (!$newId) {
            return ['ok' => false, 'error' => 'Failed to create customer account from Google profile.'];
        }

        $this->loginById((int)$newId);
        $newCustomer = $model->find((int)$newId);
        return ['ok' => true, 'customer' => $newCustomer, 'is_new' => true];
    }

    /**
     * Register a new customer with email/phone/password
     */
    public function register(array $data): array
    {
        $name = trim((string)($data['name'] ?? ''));
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $phone = trim((string)($data['phone'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if ($name === '') {
            return ['ok' => false, 'error' => 'Full name is required.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'A valid email address is required.'];
        }
        $cleanPhone = preg_replace('/\D+/', '', $phone);
        if (strlen($cleanPhone) < 10) {
            return ['ok' => false, 'error' => 'A valid 10-digit mobile number is required.'];
        }
        if (strlen($password) < 8) {
            return ['ok' => false, 'error' => 'Password must be at least 8 characters long.'];
        }

        $model = new CustomerModel();
        if ($model->findByEmail($email)) {
            return ['ok' => false, 'error' => 'An account with this email already exists. Please log in instead.'];
        }

        $customerId = $model->insert([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $cleanPhone,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        if (!$customerId) {
            return ['ok' => false, 'error' => 'Failed to create account. Please try again.'];
        }

        $this->loginById((int)$customerId);
        $customer = $model->find((int)$customerId);

        return ['ok' => true, 'customer' => $customer];
    }

    /**
     * Logout customer only (Admin session _admin_id is never touched!)
     */
    public function logout(): void
    {
        $this->session()->remove('_customer_id');
        $this->session()->regenerate(true);
    }

    /**
     * Update customer password
     */
    public function updatePassword(int $customerId, string $newPassword): void
    {
        $model = new CustomerModel();
        $model->update($customerId, [
            'password_hash'    => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token'      => null,
            'reset_expires_at' => null,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Generate password reset token
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $model = new CustomerModel();
        $customer = $model->findByEmail($email);
        if (!$customer) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $model->update((int)$customer['id'], [
            'reset_token'      => $token,
            'reset_expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')),
        ]);

        return $token;
    }

    private function recordFailedAttempt(): void
    {
        $attempts = $this->session()->get('_customer_login_attempts') ?? ['count' => 0, 'until' => 0];
        $count = (time() > ($attempts['until'] ?? 0)) ? 1 : (($attempts['count'] ?? 0) + 1);
        $this->session()->set('_customer_login_attempts', [
            'count' => $count,
            'until' => time() + 300,
        ]);
    }
}
