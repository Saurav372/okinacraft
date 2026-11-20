<?php

namespace App\Models;

use CodeIgniter\Model;

class PolicyModel extends Model
{
    protected $table = 'policies';
    protected $primaryKey = 'slug';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['slug', 'title', 'body_html', 'updated_at'];
}
