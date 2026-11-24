<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBulkTierColumnsToProducts extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('products')) {
            $fields = $this->db->getFieldNames('products');
            $toAdd = [];

            if (!in_array('enable_bulk_pricing', $fields, true)) {
                $toAdd['enable_bulk_pricing'] = [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'free_delivery',
                ];
            }

            if (!in_array('bulk_tiers_json', $fields, true)) {
                $toAdd['bulk_tiers_json'] = [
                    'type'  => 'TEXT',
                    'null'  => true,
                    'after' => 'enable_bulk_pricing',
                ];
            }

            if (!empty($toAdd)) {
                $this->forge->addColumn('products', $toAdd);
            }
        }
    }

    public function down()
    {
        // Safe non-destructive no-op
    }
}
