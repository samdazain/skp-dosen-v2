<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->userModel = new User();
    }

    /**
     * Menampilkan halaman login atau redirect ke dashboard jika sudah login
     */
    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->has('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login', [
            'pageTitle' => 'Login | SKP Dosen'
        ]);
    }

    /**
     * Memproses login user
     */
    public function login()
    {
        $nip = $this->request->getPost('nip');
        $password = $this->request->getPost('password');

        // Find user by NIP
        $user = $this->userModel->findByNIP($nip);

        if (!$user) {
            return redirect()->back()->with('error', 'NIP tidak ditemukan');
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah');
        }

        // Set session data
        $this->session->set([
            'user_id' => $user['id'],
            'user_nip' => $user['nip'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
            'isLoggedIn' => true
        ]);

        // Redirect to dashboard
        return redirect()->to('dashboard');
    }

    /**
     * Memproses logout user
     */
    public function logout()
    {
        // Clear session data
        $this->session->destroy();
        return redirect()->to('login')->with('message', 'Anda berhasil logout');
    }
}
