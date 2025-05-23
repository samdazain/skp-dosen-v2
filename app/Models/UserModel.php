<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{

    // Add this property to get detailed error information
    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    // Add this to return insert ID
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

    // For returning entity objects (optional)
    // protected $returnType = 'App\Entities\User';
    // protected $useSoftDeletes = true;
    // protected $deletedField = 'deleted_at';

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
        return $this->where('nip', $nip)->first();
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

    /**
     * Get study programs list from the migration enum
     */
    public function getStudyPrograms()
    {
        // Return friendly labels for the enum values
        return [
            'Bisnis Digital',
            'Informatika',
            'Sistem Informasi',
            'Sains Data',
            'Magister Teknologi Informasi'
        ];
    }

    // Add a method to check DB errors
    public function getLastError()
    {
        return $this->db->error();
    }

    // Override insert to add better error handling
    public function insert($data = null, bool $returnID = true)
    {
        try {
            return parent::insert($data, $returnID);
        } catch (\Exception $e) {
            log_message('error', 'Insert failed: ' . $e->getMessage());
            throw $e; // Re-throw to be caught in the controller
        }
    }
}
