<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Administrator',
                'nip' => '199001012015041001',
                'position' => 'Administrator Sistem',
                'email' => 'admin@university.ac.id',
                'role' => 'admin',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Simple Queries
        $this->db->table('users')->insertBatch($data);
    }
}
