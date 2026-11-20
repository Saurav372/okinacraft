<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantModel extends Model
{
    protected $table = 'product_variants';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'product_id', 'title', 'option1', 'option2', 'option3', 'sku',
        'price', 'compare_at_price', 'inventory_qty', 'available', 'position'
    ];
}
