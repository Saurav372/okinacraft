<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // 1. Default Admins
        $adminsToSeed = [
            [
                'name'     => 'Okina Craft Admin',
                'email'    => 'admin@okinacraft.com',
                'password' => 'OkinaCraft@2026!',
            ],
        ];

        foreach ($adminsToSeed as $adm) {
            $existing = $db->table('admins')->where('LOWER(email)', strtolower($adm['email']))->get()->getRowArray();
            if (!$existing) {
                $db->table('admins')->insert([
                    'name'          => $adm['name'],
                    'email'         => $adm['email'],
                    'password_hash' => password_hash($adm['password'], PASSWORD_DEFAULT),
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } else {
                $db->table('admins')->where('id', $existing['id'])->update([
                    'password_hash' => password_hash($adm['password'], PASSWORD_DEFAULT),
                ]);
            }
        }

        // 2. Load writable/seed_data.json
        $seedFile = WRITEPATH . 'seed_data.json';
        if (!file_exists($seedFile)) {
            return;
        }

        $seed = json_decode((string)file_get_contents($seedFile), true);
        if (!is_array($seed)) {
            return;
        }

        // Settings
        foreach (($seed['settings'] ?? []) as $k => $v) {
            $existing = $db->table('settings')->where('setting_key', $k)->get()->getRowArray();
            if (!$existing) {
                $db->table('settings')->insert([
                    'setting_key'   => $k,
                    'setting_value' => (string)$v,
                ]);
            } else {
                $db->table('settings')->where('setting_key', $k)->update([
                    'setting_value' => (string)$v,
                ]);
            }
        }

        // Policies
        foreach (($seed['policies'] ?? []) as $pol) {
            $existing = $db->table('policies')->where('slug', $pol['slug'])->get()->getRowArray();
            if (!$existing) {
                $db->table('policies')->insert([
                    'slug'       => $pol['slug'],
                    'title'      => $pol['title'],
                    'body_html'  => $pol['body_html'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $db->table('policies')->where('slug', $pol['slug'])->update([
                    'title'      => $pol['title'],
                    'body_html'  => $pol['body_html'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Collections
        $colMap = [];
        foreach (($seed['collections'] ?? []) as $col) {
            $row = $db->table('collections')->where('handle', $col['handle'])->get()->getRowArray();
            if (!$row) {
                $db->table('collections')->insert([
                    'handle'           => $col['handle'],
                    'title'            => $col['title'],
                    'description'      => $col['description'] ?? '',
                    'card_shape'       => $col['card_shape'] ?? 'standard',
                    'desktop_cols'     => (int)($col['desktop_cols'] ?? 4),
                    'show_on_home'     => (int)($col['show_on_home'] ?? 0),
                    'home_order'       => (int)($col['home_order'] ?? 99),
                    'is_free_delivery' => (int)($col['is_free_delivery'] ?? 0),
                    'created_at'       => date('Y-m-d H:i:s'),
                ]);
                $colMap[$col['handle']] = (int)$db->insertID();
            } else {
                $colMap[$col['handle']] = (int)$row['id'];
            }
        }

        // Products
        $prodMap = [];
        $sort = 1;
        foreach (($seed['products'] ?? []) as $p) {
            $row = $db->table('products')->where('handle', $p['handle'])->get()->getRowArray();
            if (!$row) {
                $db->table('products')->insert([
                    'handle'              => $p['handle'],
                    'title'               => $p['title'],
                    'body_html'           => $p['body_html'] ?? '',
                    'vendor'              => $p['vendor'] ?? 'Okina Craft',
                    'product_type'        => $p['product_type'] ?? '',
                    'tags'                => $p['tags'] ?? '',
                    'price'               => (float)($p['price'] ?? 0),
                    'compare_at_price'    => isset($p['compare_at_price']) && $p['compare_at_price'] !== '' ? (float)$p['compare_at_price'] : null,
                    'available'           => (int)($p['available'] ?? 1),
                    'allow_custom_upload' => (int)($p['allow_custom_upload'] ?? 0),
                    'free_delivery'       => (int)($p['free_delivery'] ?? 0),
                    'image_ratio'         => $p['image_ratio'] ?? '100%',
                    'options_json'        => json_encode($p['options'] ?? [], JSON_UNESCAPED_UNICODE),
                    'status'              => 'active',
                    'sort_order'          => $sort++,
                    'created_at'          => date('Y-m-d H:i:s'),
                    'updated_at'          => date('Y-m-d H:i:s'),
                ]);
                $pid = (int)$db->insertID();
            } else {
                $pid = (int)$row['id'];
            }
            $prodMap[$p['handle']] = $pid;

            // Variants
            $varCount = $db->table('product_variants')->where('product_id', $pid)->countAllResults();
            if ($varCount === 0) {
                $pos = 1;
                foreach (($p['variants'] ?? []) as $v) {
                    $db->table('product_variants')->insert([
                        'product_id'       => $pid,
                        'title'            => $v['title'] ?? 'Default Title',
                        'option1'          => $v['option1'] ?? null,
                        'option2'          => $v['option2'] ?? null,
                        'option3'          => $v['option3'] ?? null,
                        'sku'              => $v['sku'] ?? null,
                        'price'            => (float)($v['price'] ?? $p['price'] ?? 0),
                        'compare_at_price' => isset($v['compare_at_price']) && $v['compare_at_price'] !== '' ? (float)$v['compare_at_price'] : null,
                        'inventory_qty'    => (int)($v['inventory_qty'] ?? ($v['available'] ? 50 : 0)),
                        'available'        => (int)($v['available'] ?? 1),
                        'position'         => $pos++,
                    ]);
                }
            }

            // Images
            $imgCount = $db->table('product_images')->where('product_id', $pid)->countAllResults();
            if ($imgCount === 0) {
                $ipos = 1;
                foreach (($p['images'] ?? []) as $img) {
                    $db->table('product_images')->insert([
                        'product_id' => $pid,
                        'src'        => $img['src'],
                        'alt'        => $img['alt'] ?? $p['title'],
                        'width'      => (int)($img['width'] ?? 1000),
                        'height'     => (int)($img['height'] ?? 1000),
                        'position'   => $ipos++,
                    ]);
                }
            }
        }

        // Link Collections -> Products
        foreach (($seed['collections'] ?? []) as $col) {
            $cid = $colMap[$col['handle']] ?? null;
            if (!$cid) {
                continue;
            }
            $cpos = 1;
            foreach (($col['product_handles'] ?? []) as $ph) {
                $pid = $prodMap[$ph] ?? null;
                if (!$pid) {
                    continue;
                }
                $exists = $db->table('collection_products')
                    ->where('collection_id', $cid)
                    ->where('product_id', $pid)
                    ->countAllResults();
                if ($exists === 0) {
                    $db->table('collection_products')->insert([
                        'collection_id' => $cid,
                        'product_id'    => $pid,
                        'position'      => $cpos++,
                    ]);
                }
            }
        }
    }
}
