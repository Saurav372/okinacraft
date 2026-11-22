<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerAndWishlistTables extends Migration
{
    public function up()
    {
        // 1. Customers Table
        if (!$this->db->tableExists('customers')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 120],
                'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'unique' => true],
                'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'google_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'avatar_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'remember_token' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'reset_token' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'reset_expires_at' => ['type' => 'DATETIME', 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('email');
            $this->forge->addKey('google_id');
            $this->forge->createTable('customers', true);
        }

        // 2. Customer Addresses Table
        if (!$this->db->tableExists('customer_addresses')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'full_name' => ['type' => 'VARCHAR', 'constraint' => 120],
                'phone' => ['type' => 'VARCHAR', 'constraint' => 30],
                'address_line' => ['type' => 'TEXT'],
                'city' => ['type' => 'VARCHAR', 'constraint' => 100],
                'state' => ['type' => 'VARCHAR', 'constraint' => 100],
                'pincode' => ['type' => 'VARCHAR', 'constraint' => 10],
                'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('customer_id');
            $this->forge->createTable('customer_addresses', true);
        }

        // 3. Customer Wishlists Table
        if (!$this->db->tableExists('customer_wishlists')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['customer_id', 'product_id'], false, true); // Unique pair
            $this->forge->createTable('customer_wishlists', true);
        }

        // 4. Add customer_id to orders table
        if ($this->db->tableExists('orders')) {
            $orderFields = $this->db->getFieldNames('orders');
            if (!in_array('customer_id', $orderFields, true)) {
                $this->forge->addColumn('orders', [
                    'customer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'id'],
                ]);
            }
        }
    }

    public function down()
    {
        // Safe non-destructive rollbacks
    }
}
