<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGstColumns extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('products');
        if (!in_array('gst_rate', $fields, true)) {
            $this->forge->addColumn('products', [
                'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
            ]);
        }
        if (!in_array('hsn_code', $fields, true)) {
            $this->forge->addColumn('products', [
                'hsn_code' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => '6109'],
            ]);
        }

        $orderFields = $this->db->getFieldNames('orders');
        if (!in_array('tax_amount', $orderFields, true)) {
            $this->forge->addColumn('orders', [
                'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            ]);
        }

        $itemFields = $this->db->getFieldNames('order_items');
        if (!in_array('gst_rate', $itemFields, true)) {
            $this->forge->addColumn('order_items', [
                'gst_rate' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 5.00],
            ]);
        }
        if (!in_array('tax_amount', $itemFields, true)) {
            $this->forge->addColumn('order_items', [
                'tax_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            ]);
        }
    }

    public function down()
    {
        // Keep non-destructive
    }
}
