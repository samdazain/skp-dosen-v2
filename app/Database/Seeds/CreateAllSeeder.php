<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateAllSeeder extends Seeder
{
    public function run()
    {
        // Tambah user admin
        $this->db->table('users')->insert([
            'username'      => 'adminskp',
            'email'         => 'admin@gmail.com',
            'password'      => password_hash('123123', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'study_program' => 'informatika',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // Tambah satu dosen
        $this->db->table('lecturers')->insert([
            'nip'           => '123123',
            'name'          => 'admin skp',
            'email'         => 'admin@gmail.com',
            'study_program' => 'informatika',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // Ambil ID user dan dosen
        $user = $this->db->table('users')->where('email', 'admin@gmail.com')->get()->getRow();
        $lecturer = $this->db->table('lecturers')->where('nip', '123123')->get()->getRow();

        // Seed data untuk setiap aspek penilaian
        $now = date('Y-m-d H:i:s');

        $this->db->table('integrity')->insert([
            'lecturer_id' => $lecturer->id,
            'teaching_days' => 10,
            'courses_taught' => 3,
            'score' => 90,
            'updated_by' => $user->id,
            'updated_at' => $now,
        ]);

        $this->db->table('discipline')->insert([
            'lecturer_id' => $lecturer->id,
            'absence' => 0,
            'morning_absence' => 0,
            'ceremony_absence' => 0,
            'score' => 100,
            'updated_by' => $user->id,
            'updated_at' => $now,
        ]);

        $this->db->table('commitment')->insert([
            'lecturer_id' => $lecturer->id,
            'competence' => 'active',
            'tridharma_pass' => true,
            'score' => 95,
            'updated_by' => $user->id,
            'updated_at' => $now,
        ]);

        $this->db->table('cooperation')->insert([
            'lecturer_id' => $lecturer->id,
            'level' => 'very_cooperative',
            'score' => 100,
            'updated_by' => $user->id,
            'updated_at' => $now,
        ]);

        $this->db->table('service_orientation')->insert([
            'lecturer_id' => $lecturer->id,
            'questionnaire_score' => 4.8,
            'score' => 96,
            'updated_by' => $user->id,
            'updated_at' => $now,
        ]);

        $this->db->table('master_skp')->insert([
            'lecturer_id' => $lecturer->id,
            'total_score' => 96,
            'created_at' => $now,
        ]);

        $this->db->table('upload_logs')->insert([
            'filename' => 'dummy_file.xlsx',
            'category' => 'nilai_integritas',
            'uploaded_by' => $user->id,
            'uploaded_at' => $now,
        ]);

        $this->db->table('change_logs')->insert([
            'table_name' => 'integrity',
            'record_id' => 1,
            'changed_by' => $user->id,
            'action' => 'insert',
            'before' => null,
            'after' => json_encode(['score' => 90]),
            'changed_at' => $now,
        ]);
    }
}
