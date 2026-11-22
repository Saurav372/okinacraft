<?php

namespace App\Libraries;

use Config\Database;

class CatalogService
{
    private static bool $bootstrapped = false;

    public static function ensureBootstrapped(): void
    {
        if (self::$bootstrapped) {
            return;
        }
        self::$bootstrapped = true;

        $db = Database::connect();
        try {
            $hasProducts = $db->table('products')->countAllResults();
            if ($hasProducts > 0) {
                self::ensureSchemaUpgrades($db);
                return;
            }
        } catch (\Throwable) {
            // Tables do not exist yet
        }

        // Run migrations & seeder automatically
        try {
            $migrate = \Config\Services::migrations();
            $migrate->latest();
            $seeder = Database::seeder();
            $seeder->call('App\Database\Seeds\StoreSeeder');
            self::ensureSchemaUpgrades($db);
        } catch (\Throwable $e) {
            log_message('error', 'Auto bootstrap error: ' . $e->getMessage());
        }
    }

    private static function ensureSchemaUpgrades($db): void
    {
        try {
            $forge = \Config\Database::forge();
            $fields = $db->getFieldNames('products');
            if ($fields && !in_array('gst_rate', $fields, true)) {
                $forge->addColumn('products', [
                    'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
                ]);
            }
            if ($fields && !in_array('hsn_code', $fields, true)) {
                $forge->addColumn('products', [
                    'hsn_code' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => '6109'],
                ]);
            }

            $orderFields = $db->getFieldNames('orders');
            if ($orderFields && !in_array('tax_amount', $orderFields, true)) {
                $forge->addColumn('orders', [
                    'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
                ]);
            }
            if ($orderFields && !in_array('customer_id', $orderFields, true)) {
                $forge->addColumn('orders', [
                    'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                ]);
            }

            // Customer & Wishlist Tables
            if (!$db->tableExists('customers')) {
                $forge->addField([
                    'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                    'name'             => ['type' => 'VARCHAR', 'constraint' => 120],
                    'email'            => ['type' => 'VARCHAR', 'constraint' => 190, 'unique' => true],
                    'phone'            => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                    'password_hash'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'google_id'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'avatar_url'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                    'remember_token'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'reset_token'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'reset_expires_at' => ['type' => 'DATETIME', 'null' => true],
                    'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
                    'created_at'       => ['type' => 'DATETIME', 'null' => true],
                    'updated_at'       => ['type' => 'DATETIME', 'null' => true],
                ]);
                $forge->addKey('id', true);
                $forge->createTable('customers', true);
            }

            if (!$db->tableExists('customer_addresses')) {
                $forge->addField([
                    'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                    'customer_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                    'full_name'    => ['type' => 'VARCHAR', 'constraint' => 120],
                    'phone'        => ['type' => 'VARCHAR', 'constraint' => 30],
                    'address_line' => ['type' => 'TEXT'],
                    'city'         => ['type' => 'VARCHAR', 'constraint' => 100],
                    'state'        => ['type' => 'VARCHAR', 'constraint' => 100],
                    'pincode'      => ['type' => 'VARCHAR', 'constraint' => 10],
                    'is_default'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                    'created_at'   => ['type' => 'DATETIME', 'null' => true],
                    'updated_at'   => ['type' => 'DATETIME', 'null' => true],
                ]);
                $forge->addKey('id', true);
                $forge->createTable('customer_addresses', true);
            }

            if (!$db->tableExists('customer_wishlists')) {
                $forge->addField([
                    'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                    'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                    'product_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                    'created_at'  => ['type' => 'DATETIME', 'null' => true],
                ]);
                $forge->addKey('id', true);
                $forge->createTable('customer_wishlists', true);
            }

            $itemFields = $db->getFieldNames('order_items');
            if ($itemFields && !in_array('gst_rate', $itemFields, true)) {
                $forge->addColumn('order_items', [
                    'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
                ]);
            }
            if ($itemFields && !in_array('tax_amount', $itemFields, true)) {
                $forge->addColumn('order_items', [
                    'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Schema upgrade error: ' . $e->getMessage());
        }
    }

    public function getHomeCollections(): array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $cols = $db->table('collections')
            ->where('show_on_home', 1)
            ->orderBy('home_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($cols as &$col) {
            $col['products'] = $this->getProductsByCollection((int)$col['id'], 'featured');
        }
        return $cols;
    }

    public function getAllCollections(): array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $cols = $db->table('collections c')
            ->select('c.*, (SELECT COUNT(*) FROM collection_products cp WHERE cp.collection_id = c.id) AS product_count')
            ->orderBy('c.show_on_home', 'DESC')
            ->orderBy('c.home_order', 'ASC')
            ->orderBy('c.title', 'ASC')
            ->get()
            ->getResultArray();
        return $cols;
    }

    public function getCollectionByHandle(string $handle): ?array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        return $db->table('collections')->where('handle', $handle)->get()->getRowArray();
    }

    public function getCollectionById(int $id): ?array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        return $db->table('collections')->where('id', $id)->get()->getRowArray();
    }

    public function getProductsByCollection(
        int $collectionId,
        string $sort = 'featured',
        ?string $availability = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): array {
        self::ensureBootstrapped();
        $db = Database::connect();
        $builder = $db->table('products p')
            ->select('p.*')
            ->join('collection_products cp', 'cp.product_id = p.id')
            ->where('cp.collection_id', $collectionId)
            ->where('p.status', 'active');

        if ($availability === 'in_stock') {
            $builder->where('p.available', 1);
        } elseif ($availability === 'out_of_stock') {
            $builder->where('p.available', 0);
        }

        if ($minPrice !== null && $minPrice > 0) {
            $builder->where('p.price >=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice > 0) {
            $builder->where('p.price <=', $maxPrice);
        }

        $this->applySorting($builder, $sort, 'cp.position ASC, p.sort_order ASC');
        $rows = $builder->get()->getResultArray();
        return $this->hydrateProducts($rows);
    }

    public function getAllProducts(
        string $sort = 'featured',
        ?string $availability = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        bool $includeDraft = false
    ): array {
        self::ensureBootstrapped();
        $db = Database::connect();
        $builder = $db->table('products p');

        if (!$includeDraft) {
            $builder->where('p.status', 'active');
        }
        if ($availability === 'in_stock') {
            $builder->where('p.available', 1);
        } elseif ($availability === 'out_of_stock') {
            $builder->where('p.available', 0);
        }
        if ($minPrice !== null && $minPrice > 0) {
            $builder->where('p.price >=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice > 0) {
            $builder->where('p.price <=', $maxPrice);
        }

        $this->applySorting($builder, $sort, 'p.sort_order ASC, p.id ASC');
        $rows = $builder->get()->getResultArray();
        return $this->hydrateProducts($rows);
    }

    public function searchProducts(string $query, int $limit = 40): array
    {
        self::ensureBootstrapped();
        $q = trim($query);
        if ($q === '') {
            return [];
        }
        $db = Database::connect();
        $builder = $db->table('products')
            ->where('status', 'active')
            ->groupStart()
                ->like('title', $q)
                ->orLike('product_type', $q)
                ->orLike('tags', $q)
                ->orLike('body_html', $q)
                ->orLike('vendor', $q)
            ->groupEnd()
            ->orderBy("CASE WHEN title LIKE '%" . $db->escapeLikeString($q) . "%' THEN 0 ELSE 1 END", '', false)
            ->orderBy('sort_order', 'ASC')
            ->limit($limit);

        return $this->hydrateProducts($builder->get()->getResultArray());
    }

    public function getProductByHandle(string $handle): ?array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $row = $db->table('products')
            ->where('handle', $handle)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
        return $row ? $this->hydrateProduct($row) : null;
    }

    public function getProductById(int $id): ?array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $row = $db->table('products')->where('id', $id)->get()->getRowArray();
        return $row ? $this->hydrateProduct($row) : null;
    }

    public function getRelatedProducts(array $product, int $limit = 4): array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $type = (string)($product['product_type'] ?? '');
        $builder = $db->table('products')
            ->where('status', 'active')
            ->where('id !=', (int)$product['id']);

        if ($type !== '') {
            $builder->orderBy("CASE WHEN product_type = " . $db->escape($type) . " THEN 0 ELSE 1 END", '', false);
        }
        $builder->orderBy('sort_order', 'ASC')->limit($limit);

        return $this->hydrateProducts($builder->get()->getResultArray());
    }

    public function getPriceBounds(): array
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $row = $db->table('products')
            ->selectMin('price', 'min_p')
            ->selectMax('price', 'max_p')
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        return [
            'min' => (float)($row['min_p'] ?? 0),
            'max' => (float)($row['max_p'] ?? 1000),
        ];
    }

    public function hydrateProducts(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }
        $db = Database::connect();
        $ids = array_map(fn($r) => (int)$r['id'], $rows);

        $imgs = $db->table('product_images')
            ->whereIn('product_id', $ids)
            ->orderBy('position', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        $imgsByProd = [];
        foreach ($imgs as $im) {
            $imgsByProd[(int)$im['product_id']][] = $im;
        }

        $vars = $db->table('product_variants')
            ->whereIn('product_id', $ids)
            ->orderBy('position', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        $varsByProd = [];
        foreach ($vars as $v) {
            $varsByProd[(int)$v['product_id']][] = $v;
        }

        foreach ($rows as &$p) {
            $pid = (int)$p['id'];
            $p['images'] = $imgsByProd[$pid] ?? [];
            $p['variants'] = $varsByProd[$pid] ?? [];
            $p['options'] = json_decode((string)($p['options_json'] ?? '[]'), true) ?: [];
            $p['featured_image'] = $p['images'][0]['src'] ?? '';
            $p['hover_image'] = $p['images'][1]['src'] ?? '';
            $p['bulk_tiers'] = !empty($p['bulk_tiers_json']) ? (json_decode((string)$p['bulk_tiers_json'], true) ?: []) : [];
            $p['gst_rate'] = isset($p['gst_rate']) && $p['gst_rate'] !== null && $p['gst_rate'] !== '' ? (float)$p['gst_rate'] : 5.00;
            $p['hsn_code'] = !empty($p['hsn_code']) ? (string)$p['hsn_code'] : '6109';
            if (!empty($p['enable_bulk_pricing']) && !empty($p['bulk_tiers'])) {
                $bPrices = array_column($p['bulk_tiers'], 'unit_price');
                $p['min_bulk_price'] = !empty($bPrices) ? min($bPrices) : (float)$p['price'];
                $p['max_bulk_price'] = !empty($bPrices) ? max($bPrices) : (float)$p['price'];
            } else {
                $p['min_bulk_price'] = (float)$p['price'];
                $p['max_bulk_price'] = (float)$p['price'];
            }
        }
        return $rows;
    }

    public function hydrateProduct(array $p): array
    {
        $db = Database::connect();
        $pid = (int)$p['id'];
        $p['images'] = $db->table('product_images')->where('product_id', $pid)->orderBy('position', 'ASC')->get()->getResultArray();
        $p['variants'] = $db->table('product_variants')->where('product_id', $pid)->orderBy('position', 'ASC')->get()->getResultArray();
        $p['options'] = json_decode((string)($p['options_json'] ?? '[]'), true) ?: [];
        $p['featured_image'] = $p['images'][0]['src'] ?? '';
        $p['hover_image'] = $p['images'][1]['src'] ?? '';
        $p['bulk_tiers'] = !empty($p['bulk_tiers_json']) ? (json_decode((string)$p['bulk_tiers_json'], true) ?: []) : [];
        $p['gst_rate'] = isset($p['gst_rate']) && $p['gst_rate'] !== null && $p['gst_rate'] !== '' ? (float)$p['gst_rate'] : 5.00;
        $p['hsn_code'] = !empty($p['hsn_code']) ? (string)$p['hsn_code'] : '6109';
        if (!empty($p['enable_bulk_pricing']) && !empty($p['bulk_tiers'])) {
            $bPrices = array_column($p['bulk_tiers'], 'unit_price');
            $p['min_bulk_price'] = !empty($bPrices) ? min($bPrices) : (float)$p['price'];
            $p['max_bulk_price'] = !empty($bPrices) ? max($bPrices) : (float)$p['price'];
        } else {
            $p['min_bulk_price'] = (float)$p['price'];
            $p['max_bulk_price'] = (float)$p['price'];
        }

        $colRows = $db->table('collection_products')->select('collection_id')->where('product_id', $pid)->get()->getResultArray();
        $p['collection_ids'] = array_map(fn($r) => (int)$r['collection_id'], $colRows);
        return $p;
    }

    private function applySorting($builder, string $sort, string $default): void
    {
        match ($sort) {
            'title-ascending'    => $builder->orderBy('p.title', 'ASC'),
            'title-descending'   => $builder->orderBy('p.title', 'DESC'),
            'price-ascending'    => $builder->orderBy('p.price', 'ASC')->orderBy('p.id', 'ASC'),
            'price-descending'   => $builder->orderBy('p.price', 'DESC')->orderBy('p.id', 'ASC'),
            'created-descending' => $builder->orderBy('p.id', 'DESC'),
            'created-ascending'  => $builder->orderBy('p.id', 'ASC'),
            'best-selling'       => $builder->orderBy('p.available', 'DESC')->orderBy('p.sort_order', 'ASC'),
            default              => $builder->orderBy($default, '', false),
        };
    }

    public function saveProduct(?int $id, array $data, array $variants, ?array $images, array $collectionIds): int
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $db->transStart();

        $title = trim((string)($data['title'] ?? 'Untitled Product'));
        $handle = trim((string)($data['handle'] ?? '')) ?: slugify($title);

        $dup = $db->table('products')->where('handle', $handle);
        if ($id) {
            $dup->where('id !=', $id);
        }
        if ($dup->countAllResults() > 0) {
            $handle .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        $price = (float)($data['price'] ?? 0);
        $compare = ($data['compare_at_price'] ?? '') !== '' ? (float)$data['compare_at_price'] : null;
        $gstRate = isset($data['gst_rate']) && $data['gst_rate'] !== '' ? max(0, min(100, (float)$data['gst_rate'])) : 5.00;
        $hsnCode = trim((string)($data['hsn_code'] ?? '6109')) ?: '6109';
        $available = !empty($data['available']) ? 1 : 0;
        $allowCustom = !empty($data['allow_custom_upload']) ? 1 : 0;
        $freeDelivery = !empty($data['free_delivery']) ? 1 : 0;
        $enableBulk = !empty($data['enable_bulk_pricing']) ? 1 : 0;
        $bulkTiersJson = null;
        if (!empty($data['bulk_tiers_json'])) {
            $bulkTiersJson = is_string($data['bulk_tiers_json']) ? $data['bulk_tiers_json'] : json_encode($data['bulk_tiers_json'], JSON_UNESCAPED_UNICODE);
        }
        $optionsJson = json_encode($data['options'] ?? [], JSON_UNESCAPED_UNICODE);

        $prodData = [
            'handle'              => $handle,
            'title'               => $title,
            'body_html'           => (string)($data['body_html'] ?? ''),
            'vendor'              => trim((string)($data['vendor'] ?? 'Okina Craft')),
            'product_type'        => trim((string)($data['product_type'] ?? '')),
            'tags'                => trim((string)($data['tags'] ?? '')),
            'price'               => $price,
            'compare_at_price'    => $compare,
            'gst_rate'            => $gstRate,
            'hsn_code'            => $hsnCode,
            'available'           => $available,
            'allow_custom_upload' => $allowCustom,
            'free_delivery'       => $freeDelivery,
            'enable_bulk_pricing' => $enableBulk,
            'bulk_tiers_json'     => $bulkTiersJson,
            'options_json'        => $optionsJson,
            'status'              => in_array($data['status'] ?? 'active', ['active', 'draft'], true) ? $data['status'] : 'active',
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        if ($id && $id > 0) {
            $db->table('products')->where('id', $id)->update($prodData);
            $pid = $id;
        } else {
            $maxOrder = (int)$db->table('products')->selectMax('sort_order')->get()->getRowArray()['sort_order'] ?? 0;
            $prodData['sort_order'] = $maxOrder + 1;
            $prodData['created_at'] = date('Y-m-d H:i:s');
            $db->table('products')->insert($prodData);
            $pid = (int)$db->insertID();
        }

        // Variants
        $db->table('product_variants')->where('product_id', $pid)->delete();
        if (empty($variants)) {
            $variants = [[
                'title'         => 'Default Title',
                'option1'       => 'Default Title',
                'price'         => $price,
                'compare_at_price' => $compare,
                'sku'           => '',
                'inventory_qty' => $available ? 50 : 0,
                'available'     => $available,
            ]];
        }
        $pos = 1;
        $anyAvail = 0;
        foreach ($variants as $v) {
            $vAvail = !empty($v['available']) ? 1 : 0;
            if ($vAvail) $anyAvail = 1;
            $db->table('product_variants')->insert([
                'product_id'       => $pid,
                'title'            => trim((string)($v['title'] ?? 'Default Title')),
                'option1'          => $v['option1'] ?? null,
                'option2'          => $v['option2'] ?? null,
                'option3'          => $v['option3'] ?? null,
                'sku'              => $v['sku'] ?? null,
                'price'            => (float)($v['price'] ?? $price),
                'compare_at_price' => ($v['compare_at_price'] ?? '') !== '' ? (float)$v['compare_at_price'] : null,
                'inventory_qty'    => (int)($v['inventory_qty'] ?? 50),
                'available'        => $vAvail,
                'position'         => $pos++,
            ]);
        }
        $db->table('products')->where('id', $pid)->update(['available' => $available && $anyAvail ? 1 : 0]);

        // Images
        if ($images !== null) {
            $db->table('product_images')->where('product_id', $pid)->delete();
            $ipos = 1;
            foreach ($images as $img) {
                $src = trim((string)($img['src'] ?? ''));
                if ($src === '') continue;
                $db->table('product_images')->insert([
                    'product_id' => $pid,
                    'src'        => $src,
                    'alt'        => (string)($img['alt'] ?? $title),
                    'width'      => (int)($img['width'] ?? 1000),
                    'height'     => (int)($img['height'] ?? 1000),
                    'position'   => $ipos++,
                ]);
            }
        }

        // Collection mappings
        $db->table('collection_products')->where('product_id', $pid)->delete();
        foreach ($collectionIds as $cid) {
            $cid = (int)$cid;
            if ($cid > 0) {
                $db->table('collection_products')->insert([
                    'collection_id' => $cid,
                    'product_id'    => $pid,
                    'position'      => 99,
                ]);
            }
        }

        $db->transComplete();
        return $pid;
    }

    public function deleteProduct(int $id): void
    {
        self::ensureBootstrapped();
        $db = Database::connect();
        $db->table('products')->where('id', $id)->delete();
    }
}
