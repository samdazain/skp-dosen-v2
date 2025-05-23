<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'nip',
        'email',
        'password',
        'position',
        'role',
        'study_program'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPasswordIfExists'];

    /**
     * Hash password before inserting
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    /**
     * Hash password before updating only if it exists in the data
     */
    protected function hashPasswordIfExists(array $data)
    {
        // Only hash the password if it's being updated
        if (!isset($data['data']['password']) || empty($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    /**
     * Find a user by NIP
     */
    public function findByNIP(string $nip)
    {
        // In a real application, we would use:
        // return $this->where('nip', $nip)->first();

        // For demonstration purposes, using dummy data:
        foreach ($this->getDummyUsers() as $user) {
            if ($user['nip'] === $nip) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Verify if the provided password matches the stored password for a user
     */
    public function verifyPassword($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $newPassword)
    {
        return $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Get all users (with search filtering capability)
     */
    public function getAllUsers($search = null)
    {
        // In a real application, we would use query builder:
        // $builder = $this->builder();
        // if ($search) {
        //     $builder->like('name', $search)
        //             ->orLike('nip', $search)
        //             ->orLike('email', $search)
        //             ->orLike('position', $search)
        //             ->orLike('study_program', $search);
        // }
        // return $builder->get()->getResultArray();

        // For demonstration, using dummy data:
        $users = $this->getDummyUsers();

        if ($search) {
            $filteredUsers = [];
            $search = strtolower($search);

            foreach ($users as $user) {
                if (
                    strpos(strtolower($user['name'] ?? ''), $search) !== false ||
                    strpos(strtolower($user['nip'] ?? ''), $search) !== false ||
                    strpos(strtolower($user['position'] ?? ''), $search) !== false ||
                    strpos(strtolower($user['email'] ?? ''), $search) !== false ||
                    strpos(strtolower($user['role'] ?? ''), $search) !== false ||
                    (!empty($user['study_program']) && strpos(strtolower($user['study_program']), $search) !== false)
                ) {
                    $filteredUsers[] = $user;
                }
            }

            return $filteredUsers;
        }

        return $users;
    }

    /**
     * Get dummy users for demonstration purposes
     */
    private function getDummyUsers()
    {
        return [
            [
                'id' => 1,
                'name' => 'Administrator',
                'nip' => '199001012015041001',
                'position' => 'Administrator Sistem',
                'email' => 'admin@university.ac.id',
                'role' => 'admin',
                'study_program' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00'
            ],
            [
                'id' => 2,
                'name' => 'Prof. Dr. Bambang Sutopo, M.Si.',
                'nip' => '196705152000031002',
                'position' => 'Dekan Fakultas',
                'email' => 'bambang.sutopo@university.ac.id',
                'role' => 'dekan',
                'study_program' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-02 00:00:00',
                'updated_at' => '2023-01-02 00:00:00'
            ],
            [
                'id' => 3,
                'name' => 'Dr. Siti Aminah, M.T.',
                'nip' => '197508182005012003',
                'position' => 'Wakil Dekan Bidang Akademik',
                'email' => 'siti.aminah@university.ac.id',
                'role' => 'wadek1',
                'study_program' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-03 00:00:00',
                'updated_at' => '2023-01-03 00:00:00'
            ],
            [
                'id' => 4,
                'name' => 'Dr. Ahmad Fauzi, M.M.',
                'nip' => '198203102008011004',
                'position' => 'Wakil Dekan Bidang Keuangan dan SDM',
                'email' => 'ahmad.fauzi@university.ac.id',
                'role' => 'wadek2',
                'study_program' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-04 00:00:00',
                'updated_at' => '2023-01-04 00:00:00'
            ],
            [
                'id' => 5,
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'nip' => '198510202012121006',
                'position' => 'Ketua Program Studi Teknik Informatika',
                'email' => 'budi.santoso@university.ac.id',
                'role' => 'kaprodi',
                'study_program' => 'Teknik Informatika',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-05 00:00:00',
                'updated_at' => '2023-01-05 00:00:00'
            ],
            [
                'id' => 6,
                'name' => 'Dr. Dewi Lestari, S.Kom., M.Cs.',
                'nip' => '198704052014042007',
                'position' => 'Ketua Program Studi Sistem Informasi',
                'email' => 'dewi.lestari@university.ac.id',
                'role' => 'kaprodi',
                'study_program' => 'Sistem Informasi',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-06 00:00:00',
                'updated_at' => '2023-01-06 00:00:00'
            ],
            [
                'id' => 7,
                'name' => 'Agus Wijaya, S.E.',
                'nip' => '199209152018031008',
                'position' => 'Staff Akademik',
                'email' => 'agus.wijaya@university.ac.id',
                'role' => 'staff',
                'study_program' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // 'password'
                'created_at' => '2023-01-07 00:00:00',
                'updated_at' => '2023-01-07 00:00:00'
            ]
        ];
    }

    /**
     * Get study programs list
     */
    public function getStudyPrograms()
    {
        return [
            'bisnis_digital' => 'Bisnis Digital',
            'informatika' => 'Informatika',
            'sistem_informasi' => 'Sistem Informasi',
            'sains_data' => 'Sains Data',
            'magister_teknologi_informasi' => 'Magister Teknologi Informasi'
        ];
    }
}
