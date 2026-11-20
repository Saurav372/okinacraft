<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'product_id', 'variant_id', 'product_title', 'variant_title',
        'product_handle', 'image_src', 'unit_price', 'quantity', 'line_total',
        'custom_design_path', 'custom_note'
    ];
}
