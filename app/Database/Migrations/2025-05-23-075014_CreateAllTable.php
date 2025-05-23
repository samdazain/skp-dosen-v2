<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllTables extends Migration
{
    public function up()
    {
        // Users Table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'username'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'password'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3', 'kaprodi', 'staff'],
                'default' => 'staff'
            ],
            'study_program' => [
                'type' => 'ENUM',
                'constraint' => ['bisnis_digital', 'informatika', 'sistem_informasi', 'sains_data', 'magister_teknologi_informasi'],
                'null' => true
            ],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // Semesters Table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'year' => ['type' => 'YEAR'],
            'term' => ['type' => 'ENUM', 'constraint' => ['1', '2']],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('semesters');

        // Lecturers Table
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nip'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'study_program' => [
                'type' => 'ENUM',
                'constraint' => ['bisnis_digital', 'informatika', 'sistem_informasi', 'sains_data', 'magister_teknologi_informasi'],
                'null' => true
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('lecturers');

        // Score Settings Table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'category'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'range_start' => ['type' => 'FLOAT'],
            'range_end'   => ['type' => 'FLOAT'],
            'score'       => ['type' => 'TINYINT'],
            'editable'    => ['type' => 'BOOLEAN', 'default' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('score_settings');

        // Integrity Table
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id'   => ['type' => 'INT', 'unsigned' => true],
            'semester_id'   => ['type' => 'INT', 'unsigned' => true],
            'teaching_days' => ['type' => 'TINYINT'],
            'courses_taught' => ['type' => 'TINYINT'],
            'score'         => ['type' => 'TINYINT'],
            'updated_by'    => ['type' => 'INT', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('integrity');

        // Discipline Table
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id'      => ['type' => 'INT', 'unsigned' => true],
            'semester_id'      => ['type' => 'INT', 'unsigned' => true],
            'absence'          => ['type' => 'TINYINT'],
            'morning_absence'  => ['type' => 'TINYINT'],
            'ceremony_absence' => ['type' => 'TINYINT'],
            'score'            => ['type' => 'TINYINT'],
            'updated_by'       => ['type' => 'INT', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('discipline');

        // Commitment Table
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id'    => ['type' => 'INT', 'unsigned' => true],
            'semester_id'    => ['type' => 'INT', 'unsigned' => true],
            'competence'     => ['type' => 'ENUM', 'constraint' => ['active', 'inactive']],
            'tridharma_pass' => ['type' => 'BOOLEAN'],
            'score'          => ['type' => 'TINYINT'],
            'updated_by'     => ['type' => 'INT', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('commitment');

        // Cooperation Table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id' => ['type' => 'INT', 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'unsigned' => true],
            'level'       => ['type' => 'ENUM', 'constraint' => ['not_cooperative', 'fair', 'cooperative', 'very_cooperative']],
            'score'       => ['type' => 'TINYINT'],
            'updated_by'  => ['type' => 'INT', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('cooperation');

        // Service Orientation Table
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id'       => ['type' => 'INT', 'unsigned' => true],
            'semester_id'       => ['type' => 'INT', 'unsigned' => true],
            'questionnaire_score' => ['type' => 'FLOAT'],
            'score'             => ['type' => 'TINYINT'],
            'updated_by'        => ['type' => 'INT', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('service_orientation');

        // Master SKP Table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'lecturer_id'  => ['type' => 'INT', 'unsigned' => true],
            'semester_id'  => ['type' => 'INT', 'unsigned' => true],
            'total_score'  => ['type' => 'TINYINT'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id');
        $this->forge->createTable('master_skp');

        // Upload Log Table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'filename'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'category'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'uploaded_by'  => ['type' => 'INT', 'unsigned' => true],
            'uploaded_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('uploaded_by', 'users', 'id');
        $this->forge->createTable('upload_logs');

        // Change Log Table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'table_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'record_id'   => ['type' => 'INT'],
            'changed_by'  => ['type' => 'INT', 'unsigned' => true],
            'action'      => ['type' => 'ENUM', 'constraint' => ['insert', 'update', 'delete']],
            'before'      => ['type' => 'TEXT', 'null' => true],
            'after'       => ['type' => 'TEXT', 'null' => true],
            'changed_at'  => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('changed_by', 'users', 'id');
        $this->forge->createTable('change_logs');
    }

    public function down()
    {
        $this->forge->dropTable('change_logs');
        $this->forge->dropTable('upload_logs');
        $this->forge->dropTable('master_skp');
        $this->forge->dropTable('service_orientation');
        $this->forge->dropTable('cooperation');
        $this->forge->dropTable('commitment');
        $this->forge->dropTable('discipline');
        $this->forge->dropTable('integrity');
        $this->forge->dropTable('score_settings');
        $this->forge->dropTable('lecturers');
        $this->forge->dropTable('semesters');
        $this->forge->dropTable('users');
    }
}
