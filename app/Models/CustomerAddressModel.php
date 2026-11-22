<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerAddressModel extends Model
{
    protected $table = 'customer_addresses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'customer_id',
        'full_name',
        'phone',
        'address_line',
        'city',
        'state',
        'pincode',
        'is_default',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function getByCustomer(int $customerId): array
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    public function getDefault(int $customerId): ?array
    {
        $default = $this->where('customer_id', $customerId)->where('is_default', 1)->first();
        if ($default) {
            return $default;
        }
        return $this->where('customer_id', $customerId)->orderBy('id', 'DESC')->first();
    }

    public function setDefault(int $customerId, int $addressId): void
    {
        $this->where('customer_id', $customerId)->set(['is_default' => 0])->update();
        $this->where('id', $addressId)->where('customer_id', $customerId)->set(['is_default' => 1])->update();
    }
}
