<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStoreTables extends Migration
{
    public function up()
    {
        // 1. Admins
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('admins', true);

        // 2. Settings
        $this->forge->addField([
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 120],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('setting_key', true);
        $this->forge->createTable('settings', true);

        // 3. Policies
        $this->forge->addField([
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'body_html' => ['type' => 'TEXT'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('slug', true);
        $this->forge->createTable('policies', true);

        // 4. Collections
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'handle' => ['type' => 'VARCHAR', 'constraint' => 200, 'unique' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'card_shape' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'standard'],
            'desktop_cols' => ['type' => 'INT', 'constraint' => 11, 'default' => 4],
            'show_on_home' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'home_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 99],
            'is_free_delivery' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('collections', true);

        // 5. Products
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'handle' => ['type' => 'VARCHAR', 'constraint' => 220, 'unique' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 300],
            'body_html' => ['type' => 'TEXT', 'null' => true],
            'vendor' => ['type' => 'VARCHAR', 'constraint' => 120, 'default' => 'Okina Craft'],
            'product_type' => ['type' => 'VARCHAR', 'constraint' => 120, 'default' => ''],
            'tags' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'compare_at_price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
            'hsn_code' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => '6109'],
            'available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'allow_custom_upload' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'free_delivery' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'image_ratio' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => '100%'],
            'options_json' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('products', true);

        // 6. Product Variants
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Default Title'],
            'option1' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'option2' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'option3' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'sku' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'compare_at_price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'inventory_qty' => ['type' => 'INT', 'constraint' => 11, 'default' => 50],
            'available' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->createTable('product_variants', true);

        // 7. Product Images
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'src' => ['type' => 'VARCHAR', 'constraint' => 500],
            'alt' => ['type' => 'VARCHAR', 'constraint' => 300, 'default' => ''],
            'width' => ['type' => 'INT', 'constraint' => 11, 'default' => 1000],
            'height' => ['type' => 'INT', 'constraint' => 11, 'default' => 1000],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->createTable('product_images', true);

        // 8. Collection Products
        $this->forge->addField([
            'collection_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->forge->addKey(['collection_id', 'product_id'], true);
        $this->forge->createTable('collection_products', true);

        // 9. Orders
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_number' => ['type' => 'VARCHAR', 'constraint' => 40, 'unique' => true],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'customer_email' => ['type' => 'VARCHAR', 'constraint' => 190],
            'customer_phone' => ['type' => 'VARCHAR', 'constraint' => 40],
            'shipping_address' => ['type' => 'TEXT'],
            'shipping_city' => ['type' => 'VARCHAR', 'constraint' => 100],
            'shipping_state' => ['type' => 'VARCHAR', 'constraint' => 100],
            'shipping_pincode' => ['type' => 'VARCHAR', 'constraint' => 20],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'cod'],
            'payment_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'order_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'shipping_cost' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'customer_note' => ['type' => 'TEXT', 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 120, 'default' => ''],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('orders', true);

        // 10. Order Items
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'variant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'product_title' => ['type' => 'VARCHAR', 'constraint' => 300],
            'variant_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
            'product_handle' => ['type' => 'VARCHAR', 'constraint' => 220, 'default' => ''],
            'image_src' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'unit_price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'quantity' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
            'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'line_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'custom_design_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
            'custom_note' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->createTable('order_items', true);

        // 11. Contact Messages
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => ''],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => ''],
            'message' => ['type' => 'TEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unread'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('contact_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('contact_messages', true);
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('collection_products', true);
        $this->forge->dropTable('product_images', true);
        $this->forge->dropTable('product_variants', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('collections', true);
        $this->forge->dropTable('policies', true);
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('admins', true);
    }
}
