<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerWishlistModel extends Model
{
    protected $table = 'customer_wishlists';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'customer_id',
        'product_id',
        'created_at',
    ];
    protected $useTimestamps = false;

    public function getProductIds(int $customerId): array
    {
        $rows = $this->select('product_id')
                     ->where('customer_id', $customerId)
                     ->findAll();
        return array_map(fn($r) => (int)$r['product_id'], $rows);
    }

    public function toggle(int $customerId, int $productId): bool
    {
        $existing = $this->where('customer_id', $customerId)
                         ->where('product_id', $productId)
                         ->first();

        if ($existing) {
            $this->where('id', (int)$existing['id'])->delete();
            return false; // Removed
        }

        $this->insert([
            'customer_id' => $customerId,
            'product_id'  => $productId,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        return true; // Added
    }

    public function syncGuestItems(int $customerId, array $productIds): array
    {
        $productIds = array_filter(array_map('intval', $productIds), fn($id) => $id > 0);
        if (empty($productIds)) {
            return $this->getProductIds($customerId);
        }

        $existing = $this->getProductIds($customerId);
        $toInsert = array_diff($productIds, $existing);

        foreach ($toInsert as $pid) {
            $this->insert([
                'customer_id' => $customerId,
                'product_id'  => $pid,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->getProductIds($customerId);
    }
}
