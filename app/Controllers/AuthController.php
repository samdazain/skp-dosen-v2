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
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nip' => 'required',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'NIP dan password harus diisi');
        }

        $nip = $this->request->getPost('nip');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByNIP($nip);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'NIP tidak ditemukan');
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        $sessionData = [
            'user_id' => $user['id'] ?? null,
            'user_nip' => $user['nip'] ?? '',
            'user_name' => $user['name'] ?? 'User',
            'user_role' => $user['role'] ?? 'staff',
            'isLoggedIn' => true
        ];

        if (isset($user['email'])) {
            $sessionData['user_email'] = $user['email'];
        }

        if (isset($user['study_program'])) {
            $sessionData['user_study_program'] = $user['study_program'];
        }

        $this->session->set($sessionData);

        return redirect()->to('dashboard');
    }

    public function logout()
    {
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
