<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedAtToCommitment extends Migration
{
    public function up()
    {
        // Add created_at field if it doesn't exist
        if (!$this->db->fieldExists('created_at', 'commitment')) {
            $this->forge->addColumn('commitment', [
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_by'
                ]
            ]);
        }

        // Add default values for existing records
        $this->db->query("UPDATE commitment SET created_at = updated_at WHERE created_at IS NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('commitment', 'created_at');
    }
}
