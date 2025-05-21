<?php

namespace App\Controllers;

use App\Models\UserModel;

class SettingController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display the settings page
     */
    public function index()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'id' => session()->get('user_id'),
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role'),
        ];

        return view('settings/index', [
            'pageTitle' => 'Pengaturan Akun | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Validate input
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('settings')->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // In a real application, you would verify the current password against the database
        // For demonstration, we'll simulate password verification

        // Fetch user from database
        // $user = $this->userModel->find($userId);

        // For demonstration, we'll use dummy verification
        $isCurrentPasswordCorrect = $this->verifyCurrentPassword($userId, $currentPassword);

        if (!$isCurrentPasswordCorrect) {
            return redirect()->to('settings')->with('error', 'Password saat ini tidak valid');
        }

        // Update password in database
        // $this->userModel->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);

        // For demonstration, we'll just show success message
        $this->updateUserPassword($userId, $newPassword);

        return redirect()->to('settings')->with('success', 'Password berhasil diubah');
    }

    /**
     * Verify if the provided current password is correct
     * In real implementation, this should check against database
     */
    private function verifyCurrentPassword($userId, $currentPassword)
    {
        // In a real application, you would:
        // 1. Fetch the user from the database
        // 2. Check if the provided password matches the stored hashed password

        // For demonstration purposes, we'll assume the verification passes
        return true;
    }

    /**
     * Update user password
     * In real implementation, this should update the database
     */
    private function updateUserPassword($userId, $newPassword)
    {
        // In a real application, you would:
        // 1. Hash the new password
        // 2. Update the user record in the database

        // For demonstration purposes, we'll just return true
        return true;
    }
}
