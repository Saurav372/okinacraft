<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_number', 'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode',
        'payment_method', 'payment_status', 'order_status',
        'subtotal', 'tax_amount', 'shipping_cost', 'total', 'customer_note',
        'tracking_number', 'created_at', 'updated_at'
    ];
}
