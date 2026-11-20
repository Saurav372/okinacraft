<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'setting_key';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['setting_key', 'setting_value'];

    public function getAllAsMap(): array
    {
        $rows = $this->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['setting_key']] = $r['setting_value'];
        }
        return $map;
    }

    public function setSetting(string $key, string $value): void
    {
        $existing = $this->find($key);
        if ($existing) {
            $this->update($key, ['setting_value' => $value]);
        } else {
            $this->insert(['setting_key' => $key, 'setting_value' => $value]);
        }
    }
}
