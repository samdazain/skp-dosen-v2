<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScoreSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'unsigned' => true
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Category: integrity, discipline, commitment, cooperation, orientation'
            ],
            'subcategory' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Subcategory within each category'
            ],
            'range_type' => [
                'type' => 'ENUM',
                'constraint' => ['range', 'above', 'below', 'exact', 'fixed', 'boolean'],
                'default' => 'range',
                'comment' => 'Type of range: range(x-y), above(>x), below(<x), exact(=x), fixed(label), boolean(yes/no)'
            ],
            'range_start' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Start value of range (null for some types)'
            ],
            'range_end' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'End value of range (null for some types)'
            ],
            'range_label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Text label for fixed/boolean types'
            ],
            'score' => [
                'type' => 'TINYINT',
                'unsigned' => true,
                'comment' => 'Score value (0-100, integer only - no decimals)'
            ],
            'editable' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'comment' => 'Whether this range can be edited by admin'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['category', 'subcategory']);
        $this->forge->addKey(['range_type']);
        $this->forge->createTable('score_settings');
    }

    public function down()
    {
        $this->forge->dropTable('score_settings');
    }
}
