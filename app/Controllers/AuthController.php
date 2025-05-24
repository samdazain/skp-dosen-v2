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
            sleep(1);
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

    public function changePassword()
    {
        if (!$this->session->has('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Anda harus login terlebih dahulu');
        }

        if ($this->request->getMethod() !== 'post') {
            return view('auth/change_password', [
                'pageTitle' => 'Ubah Password | SKP Dosen',
                'user' => [
                    'name' => $this->session->get('user_name'),
                    'role' => $this->session->get('user_role'),
                ]
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => [
                'rules' => 'required|min_length[6]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/]',
                'errors' => [
                    'regex_match' => 'Password baru harus mengandung minimal satu huruf dan satu angka'
                ]
            ],
            'confirm_password' => 'required|matches[new_password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak valid');
        }

        $this->userModel->update($userId, [
            'password' => $this->request->getPost('new_password')
        ]);

        return redirect()->to('dashboard')->with('success', 'Password berhasil diubah');
    }
}
