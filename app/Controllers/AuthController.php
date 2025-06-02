<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $session;
    protected $userModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->userModel = new UserModel();
        helper($this->helpers);
    }

    public function index()
    {
        if ($this->session->has('isLoggedIn')) {
            return redirect()->to('dashboard');
        }

        return view('auth/login', [
            'pageTitle' => 'Login | SKP Dosen'
        ]);
    }

    public function login()
    {
        // Enhanced debugging for login process
        log_message('info', 'Login attempt started');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nip' => 'required|trim',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('warning', 'Login validation failed: ' . json_encode($validation->getErrors()));
            return redirect()->back()->withInput()->with('error', 'NIP dan password harus diisi');
        }

        $nip = trim($this->request->getPost('nip'));
        $password = $this->request->getPost('password');

        log_message('info', "Login attempt for NIP: {$nip}");

        // Check if UserModel exists and is working
        if (!$this->userModel) {
            log_message('error', 'UserModel not initialized');
            return redirect()->back()->withInput()->with('error', 'Sistem error: Model tidak tersedia');
        }

        try {
            $user = $this->userModel->findByNIP($nip);
            log_message('info', 'User lookup result: ' . ($user ? 'found' : 'not found'));

            if (!$user) {
                log_message('warning', "NIP not found: {$nip}");
                return redirect()->back()->withInput()->with('error', 'NIP tidak ditemukan');
            }

            // Debug user data (without password)
            $userDebug = $user;
            unset($userDebug['password']);
            log_message('info', 'User data: ' . json_encode($userDebug));

            // Verify password
            $passwordVerified = $this->userModel->verifyPassword($password, $user['password']);
            log_message('info', 'Password verification: ' . ($passwordVerified ? 'success' : 'failed'));

            if (!$passwordVerified) {
                log_message('warning', "Invalid password for NIP: {$nip}");
                return redirect()->back()->withInput()->with('error', 'Password salah');
            }

            // Create session data
            $sessionData = [
                'user_id' => $user['id'] ?? null,
                'user_nip' => $user['nip'] ?? '',
                'user_name' => $user['name'] ?? 'User',
                'user_role' => $user['role'] ?? 'staff',
                'user_email' => $user['email'] ?? '',
                'user_study_program' => $user['study_program'] ?? '',
                'isLoggedIn' => true
            ];

            log_message('info', 'Setting session data: ' . json_encode($sessionData));

            // Set session
            $this->session->set($sessionData);

            // Verify session was set
            $sessionSet = $this->session->get('isLoggedIn');
            log_message('info', 'Session verification after set: ' . ($sessionSet ? 'success' : 'failed'));

            if (!$sessionSet) {
                log_message('error', 'Failed to set session data');
                return redirect()->back()->withInput()->with('error', 'Gagal membuat sesi login');
            }

            log_message('info', "Successful login for user: {$user['name']} (NIP: {$nip})");

            // Redirect to dashboard
            return redirect()->to('dashboard')->with('success', 'Login berhasil! Selamat datang ' . $user['name']);
        } catch (\Exception $e) {
            log_message('error', 'Login exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat login');
        }
    }

    public function logout()
    {
        log_message('info', 'User logout: ' . ($this->session->get('user_name') ?? 'unknown'));
        $this->session->destroy();
        return redirect()->to('login')->with('message', 'Anda berhasil logout');
    }

    public function settings()
    {
        if (!$this->session->has('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'id' => $this->session->get('user_id'),
            'name' => $this->session->get('user_name'),
            'email' => $this->session->get('user_email'),
            'role' => $this->session->get('user_role'),
        ];

        return view('auth/settings', [
            'pageTitle' => 'Pengaturan Akun | SKP Dosen',
            'user' => $userData
        ]);
    }

    public function changePassword()
    {
        if (!$this->session->has('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => [
                'rules' => 'required|min_length[6]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/]',
                'errors' => [
                    'regex_match' => 'Password baru harus mengandung minimal satu huruf dan satu angka'
                ]
            ],
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('settings')->with('errors', $this->validator->getErrors());
        }

        $userId = $this->session->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('settings')->with('error', 'User tidak ditemukan');
        }

        if (!$this->userModel->verifyPassword($currentPassword, $user['password'])) {
            return redirect()->to('settings')->with('error', 'Password saat ini tidak valid');
        }

        $this->userModel->update($userId, ['password' => $newPassword]);

        return redirect()->to('settings')->with('success', 'Password berhasil diubah');
    }
}
