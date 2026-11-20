<?php

namespace App\Models;

use CodeIgniter\Model;

class CollectionModel extends Model
{
    protected $table = 'collections';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'handle', 'title', 'description', 'card_shape',
        'desktop_cols', 'show_on_home', 'home_order', 'is_free_delivery', 'created_at'
    ];
}
