<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nip' => '123123',
            'name' => 'Administrator',
            'email' => 'admin@fasilkom.com',
            'password' => password_hash('qweqwe123', PASSWORD_DEFAULT),
            'position' => 'System Administrator',
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert the admin user
        $this->db->table('users')->insert($data);
    }
}
