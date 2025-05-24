<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LecturerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nip' => '198702142021211001',
            'name' => 'Sugiarto, S.Kom., M.Kom',
            'email' => 'sugiarto@fasilkom.com',
            'study_program' => 'Bisnis Digital',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert the admin user
        $this->db->table('lecturers')->insert($data);
    }
}
