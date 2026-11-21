<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'handle', 'title', 'body_html', 'vendor', 'product_type', 'tags',
        'price', 'compare_at_price', 'gst_rate', 'hsn_code', 'available', 'allow_custom_upload',
        'free_delivery', 'enable_bulk_pricing', 'bulk_tiers_json',
        'image_ratio', 'options_json', 'status', 'sort_order',
        'created_at', 'updated_at'
    ];
}
