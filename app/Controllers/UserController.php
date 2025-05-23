<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $helpers = ['form', 'url', 'role'];

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper($this->helpers);
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        $search = $this->request->getGet('search');
        $users = $this->userModel->getAllUsers($search);

        return view('user/index', [
            'pageTitle' => 'Manajemen Pengguna | SKP Dosen',
            'user' => $userData,
            'users' => $users,
            'search' => $search
        ]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat menambah pengguna baru');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        $studyPrograms = $this->userModel->getStudyPrograms();

        return view('user/create', [
            'pageTitle' => 'Tambah Pengguna | SKP Dosen',
            'user' => $userData,
            'studyPrograms' => $studyPrograms ?? []
        ]);
    }

    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat menambah pengguna baru');
        }

        try {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required',
                'nip' => 'required|numeric|is_unique[users.nip]',
                'position' => 'required',
                'email' => 'required|valid_email|is_unique[users.email]',
                'role' => 'required|in_list[admin,dekan,wadek1,wadek2,wadek3,kaprodi,staff]',
                'password' => [
                    'rules' => 'required|min_length[6]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/]',
                    'errors' => ['regex_match' => 'Password harus mengandung minimal satu huruf dan satu angka']
                ],
                'password_confirm' => 'required|matches[password]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            // Validasi manual untuk role kaprodi
            if ($this->request->getPost('role') === 'kaprodi' && empty($this->request->getPost('study_program'))) {
                return redirect()->back()->withInput()->with('error', 'Program studi wajib diisi untuk role kaprodi');
            }

            $data = [
                'name' => htmlspecialchars($this->request->getPost('name')),
                'nip' => $this->request->getPost('nip'),
                'position' => htmlspecialchars($this->request->getPost('position')),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ];

            $studyProgram = $this->request->getPost('study_program');
            if (!empty($studyProgram)) {
                $data['study_program'] = $studyProgram;
            }

            $userToLog = $data;
            unset($userToLog['password']);
            log_message('debug', 'Saving user data: ' . print_r($userToLog, true));

            $userId = $this->userModel->insert($data);

            if ($userId) {
                log_message('debug', 'User saved successfully with ID: ' . $userId);
            } else {
                log_message('error', 'Failed to save user. DB errors: ' . print_r($this->userModel->errors(), true));
            }

            return redirect()->to('user')->with('success', 'Pengguna baru berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error creating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat mengubah data pengguna');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('user')->with('error', 'Pengguna tidak ditemukan');
        }

        $studyPrograms = $this->userModel->getStudyPrograms();

        return view('user/edit', [
            'pageTitle' => 'Edit Pengguna | SKP Dosen',
            'user' => $userData,
            'userData' => $user,
            'studyPrograms' => $studyPrograms ?? []
        ]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat mengubah data pengguna');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('user')->with('error', 'Pengguna tidak ditemukan');
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'position' => 'required',
            'role' => 'required|in_list[admin,dekan,wadek1,wadek2,wadek3,kaprodi,staff]',
            'study_program' => 'permit_empty',
        ];

        if ($user['email'] !== $this->request->getPost('email')) {
            $rules['email'] = 'required|valid_email|is_unique[users.email]';
        } else {
            $rules['email'] = 'required|valid_email';
        }

        if ($user['nip'] !== $this->request->getPost('nip')) {
            $rules['nip'] = 'required|numeric|is_unique[users.nip]';
        } else {
            $rules['nip'] = 'required|numeric';
        }

        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'rules' => 'min_length[6]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/]',
                'errors' => ['regex_match' => 'Password harus mengandung minimal satu huruf dan satu angka']
            ];
            $rules['password_confirm'] = 'matches[password]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        if ($this->request->getPost('role') === 'kaprodi' && empty($this->request->getPost('study_program'))) {
            return redirect()->back()->withInput()->with('error', 'Program studi wajib diisi untuk role kaprodi');
        }

        $data = [
            'name' => htmlspecialchars($this->request->getPost('name')),
            'nip' => $this->request->getPost('nip'),
            'position' => htmlspecialchars($this->request->getPost('position')),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'study_program' => $this->request->getPost('study_program')
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('user')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat menghapus pengguna');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('user')->with('error', 'Pengguna tidak ditemukan');
        }

        $this->userModel->delete($id);

        return redirect()->to('user')->with('success', 'Pengguna berhasil dihapus');
    }
}
