<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowCallbacks = true;

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

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    protected function hashPasswordIfExists(array $data)
    {
        if (!isset($data['data']['password']) || empty($data['data']['password'])) {
            return $data;
        }

        if (password_get_info($data['data']['password'])['algo'] === 0) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    public function findByNIP(string $nip)
    {
        return $this->where('nip', $nip)->first();
    }

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function verifyPassword(string $inputPassword, string $storedHash): bool
    {
        return password_verify($inputPassword, $storedHash);
    }

    public function changePassword(int $userId, string $newPassword)
    {
        return $this->update($userId, [
            'password' => $newPassword
        ]);
    }

    public function getAllUsers($search = null)
    {
        $builder = $this->builder();

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('nip', $search)
                ->orLike('email', $search)
                ->orLike('position', $search)
                ->orLike('study_program', $search)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function getStudyPrograms()
    {
        return [
            'Bisnis Digital',
            'Informatika',
            'Sistem Informasi',
            'Sains Data',
            'Magister Teknologi Informasi'
        ];
    }

    public function getLastError()
    {
        return $this->db->error();
    }

    public function insert($data = null, bool $returnID = true)
    {
        try {
            return parent::insert($data, $returnID);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function findByRole(string $role)
    {
        return $this->where('role', $role)->findAll();
    }

    public function countByRole(string $role)
    {
        return $this->where('role', $role)->countAllResults();
    }

    public function getStatistics()
    {
        $stats = [
            'total' => $this->countAll(),
            'admin' => $this->countByRole('admin'),
            'dekan' => $this->countByRole('dekan'),
            'kaprodi' => $this->countByRole('kaprodi'),
            'dosen' => $this->countByRole('dosen'),
            'staff' => $this->countByRole('staff'),
        ];

        return $stats;
    }
}
