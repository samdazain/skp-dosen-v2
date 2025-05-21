<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nip',
        'password',
        'name',
        'role'
    ];

    // For testing purposes, we'll use these dummy users
    private $dummyUsers = [
        [
            'id' => 1,
            'nip' => '123456789',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password is 'password'
            'name' => 'Dr. John Doe',
            'role' => 'dosen'
        ],
        [
            'id' => 2,
            'nip' => '987654321',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password is 'password'
            'name' => 'Prof. Jane Smith',
            'role' => 'dosen'
        ],
        [
            'id' => 3,
            'nip' => 'admin',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password is 'password'
            'name' => 'Administrator',
            'role' => 'admin'
        ]
    ];

    /**
     * Find a user by NIP
     * This method simulates database lookup
     */
    public function findByNIP(string $nip)
    {
        // In a real application, this would query the database
        foreach ($this->dummyUsers as $user) {
            if ($user['nip'] === $nip) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Verify if the provided password matches the user's password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
