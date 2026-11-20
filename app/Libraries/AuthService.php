<?php

namespace App\Libraries;

use App\Models\AdminModel;

class AuthService
{
    private function session()
    {
        return \Config\Services::session();
    }

    public function admin(): ?array
    {
        $id = $this->session()->get('_admin_id');
        if (!$id) {
            return null;
        }
        $model = new AdminModel();
        $admin = $model->find((int)$id);
        if (!$admin) {
            $this->session()->remove('_admin_id');
            return null;
        }
        return $admin;
    }

    public function check(): bool
    {
        return $this->admin() !== null;
    }

    public function attempt(string $email, string $password): array
    {
        // Rate limiting in session
        $attempts = $this->session()->get('_login_attempts') ?? ['count' => 0, 'until' => 0];
        if (time() < ($attempts['until'] ?? 0) && ($attempts['count'] ?? 0) >= 6) {
            $wait = max(1, (int)ceil(($attempts['until'] - time()) / 60));
            return ['ok' => false, 'error' => "Too many failed login attempts. Try again in {$wait} minute(s)."];
        }

        $model = new AdminModel();
        $admin = $model->where('LOWER(email)', strtolower(trim($email)))->first();
        if (!$admin || !password_verify($password, (string)$admin['password_hash'])) {
            $count = (time() > ($attempts['until'] ?? 0)) ? 1 : (($attempts['count'] ?? 0) + 1);
            $this->session()->set('_login_attempts', [
                'count' => $count,
                'until' => time() + 300,
            ]);
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        $this->session()->remove('_login_attempts');
        $this->session()->regenerate(true);
        $this->session()->set('_admin_id', (int)$admin['id']);
        return ['ok' => true, 'admin' => $admin];
    }

    public function logout(): void
    {
        $this->session()->remove('_admin_id');
        $this->session()->regenerate(true);
    }

    public function updatePassword(int $adminId, string $newPassword): void
    {
        $model = new AdminModel();
        $model->update($adminId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);
    }
}
