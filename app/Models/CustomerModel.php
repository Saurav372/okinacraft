<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'password_hash',
        'google_id',
        'avatar_url',
        'remember_token',
        'reset_token',
        'reset_expires_at',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }
        return $this->where('LOWER(email)', $email)->first();
    }

    public function findByPhone(string $phone): ?array
    {
        $clean = preg_replace('/\D+/', '', $phone);
        if (strlen($clean) < 10) {
            return null;
        }
        $last10 = substr($clean, -10);
        return $this->like('phone', $last10)->first();
    }

    public function findByGoogleId(string $googleId): ?array
    {
        $googleId = trim($googleId);
        if ($googleId === '') {
            return null;
        }
        return $this->where('google_id', $googleId)->first();
    }

    public function findByResetToken(string $token): ?array
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }
        $customer = $this->where('reset_token', $token)->first();
        if (!$customer) {
            return null;
        }

        if (!empty($customer['reset_expires_at']) && strtotime($customer['reset_expires_at']) < time()) {
            return null; // Expired token
        }

        return $customer;
    }
}
