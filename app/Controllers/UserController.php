<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display list of users
     */
    public function index()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        // Check if search parameter exists
        $search = $this->request->getGet('search');

        // In a real application, you would fetch from database
        // For now, we'll use dummy data with search filtering if needed
        $users = $this->getDummyUsers($search);

        return view('user/index', [
            'pageTitle' => 'Manajemen Pengguna | SKP Dosen',
            'user' => $userData,
            'users' => $users,
            'search' => $search
        ]);
    }

    /**
     * Display form to create a new user
     */
    public function create()
    {
        // Ensure user is logged in and is admin
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

        // Get list of study programs for dropdown
        $studyPrograms = $this->getStudyPrograms();

        return view('user/create', [
            'pageTitle' => 'Tambah Pengguna | SKP Dosen',
            'user' => $userData,
            'studyPrograms' => $studyPrograms
        ]);
    }

    /**
     * Store a new user
     */
    public function store()
    {
        // Ensure user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat menambah pengguna baru');
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'nip' => 'required|numeric|min_length[18]|max_length[18]',
            'position' => 'required',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,dekan,wadek1,wadek2,wadek3,kaprodi,staff]',
            'study_program' => 'required_if[role,kaprodi]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Get form data
        $data = [
            'name' => $this->request->getPost('name'),
            'nip' => $this->request->getPost('nip'),
            'position' => $this->request->getPost('position'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'study_program' => $this->request->getPost('study_program'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ];

        // In a real application, you would save to database
        // $this->userModel->save($data);

        return redirect()->to('user')->with('success', 'Pengguna baru berhasil ditambahkan');
    }

    /**
     * Display form to edit a user
     */
    public function edit($id)
    {
        // Ensure user is logged in and is admin
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

        // In a real application, you would fetch from database
        // $user = $this->userModel->find($id);
        $user = $this->getUserById($id);

        if (!$user) {
            return redirect()->to('user')->with('error', 'Pengguna tidak ditemukan');
        }

        // Get list of study programs for dropdown
        $studyPrograms = $this->getStudyPrograms();

        return view('user/edit', [
            'pageTitle' => 'Edit Pengguna | SKP Dosen',
            'user' => $userData,
            'userData' => $user,
            'studyPrograms' => $studyPrograms
        ]);
    }

    /**
     * Update user data
     */
    public function update($id)
    {
        // Ensure user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat mengubah data pengguna');
        }

        // Validate input
        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required',
            'nip' => 'required|numeric|min_length[18]|max_length[18]',
            'position' => 'required',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,dekan,wadek1,wadek2,wadek3,kaprodi,staff]',
            'study_program' => 'required_if[role,kaprodi]',
        ];

        // Only validate password if provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Get form data
        $data = [
            'name' => $this->request->getPost('name'),
            'nip' => $this->request->getPost('nip'),
            'position' => $this->request->getPost('position'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'study_program' => $this->request->getPost('study_program'),
        ];

        // Update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // In a real application, you would update the database
        // $this->userModel->update($id, $data);

        return redirect()->to('user')->with('success', 'Data pengguna berhasil diperbarui');
    }

    /**
     * Delete a user
     */
    public function delete($id)
    {
        // Ensure user is logged in and is admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('user')->with('error', 'Hanya admin yang dapat menghapus pengguna');
        }

        // In a real application, you would delete from database
        // $this->userModel->delete($id);

        return redirect()->to('user')->with('success', 'Pengguna berhasil dihapus');
    }

    /**
     * Get dummy user data
     */
    private function getDummyUsers($search = null)
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Administrator',
                'nip' => '199001012015041001',
                'position' => 'Administrator Sistem',
                'email' => 'admin@university.ac.id',
                'role' => 'admin',
                'study_program' => null
            ],
            [
                'id' => 2,
                'name' => 'Prof. Dr. Bambang Sutopo, M.Si.',
                'nip' => '196705152000031002',
                'position' => 'Dekan Fakultas',
                'email' => 'bambang.sutopo@university.ac.id',
                'role' => 'dekan',
                'study_program' => null
            ],
            [
                'id' => 3,
                'name' => 'Dr. Siti Aminah, M.T.',
                'nip' => '197508182005012003',
                'position' => 'Wakil Dekan Bidang Akademik',
                'email' => 'siti.aminah@university.ac.id',
                'role' => 'wadek1',
                'study_program' => null
            ],
            [
                'id' => 4,
                'name' => 'Dr. Ahmad Fauzi, M.M.',
                'nip' => '198203102008011004',
                'position' => 'Wakil Dekan Bidang Keuangan dan SDM',
                'email' => 'ahmad.fauzi@university.ac.id',
                'role' => 'wadek2',
                'study_program' => null
            ],
            [
                'id' => 5,
                'name' => 'Dr. Rini Susanti, M.Hum.',
                'nip' => '197912152010012005',
                'position' => 'Wakil Dekan Bidang Kemahasiswaan',
                'email' => 'rini.susanti@university.ac.id',
                'role' => 'wadek3',
                'study_program' => null
            ],
            [
                'id' => 6,
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'nip' => '198510202012121006',
                'position' => 'Ketua Program Studi Teknik Informatika',
                'email' => 'budi.santoso@university.ac.id',
                'role' => 'kaprodi',
                'study_program' => 'Teknik Informatika'
            ],
            [
                'id' => 7,
                'name' => 'Dr. Dewi Lestari, S.Kom., M.Cs.',
                'nip' => '198704052014042007',
                'position' => 'Ketua Program Studi Sistem Informasi',
                'email' => 'dewi.lestari@university.ac.id',
                'role' => 'kaprodi',
                'study_program' => 'Sistem Informasi'
            ],
            [
                'id' => 8,
                'name' => 'Agus Wijaya, S.E.',
                'nip' => '199209152018031008',
                'position' => 'Staff Akademik',
                'email' => 'agus.wijaya@university.ac.id',
                'role' => 'staff',
                'study_program' => null
            ],
            [
                'id' => 9,
                'name' => 'Sari Nurhayati, A.Md.',
                'nip' => '199506072020122009',
                'position' => 'Staff Keuangan',
                'email' => 'sari.nurhayati@university.ac.id',
                'role' => 'staff',
                'study_program' => null
            ]
        ];

        // Filter users by search term if provided
        if ($search) {
            $filteredUsers = [];
            $search = strtolower($search);

            foreach ($users as $user) {
                if (
                    strpos(strtolower($user['name']), $search) !== false ||
                    strpos(strtolower($user['nip']), $search) !== false ||
                    strpos(strtolower($user['position']), $search) !== false ||
                    strpos(strtolower($user['email']), $search) !== false ||
                    strpos(strtolower($user['role']), $search) !== false ||
                    ($user['study_program'] && strpos(strtolower($user['study_program']), $search) !== false)
                ) {
                    $filteredUsers[] = $user;
                }
            }

            return $filteredUsers;
        }

        return $users;
    }

    /**
     * Get user data by ID
     */
    private function getUserById($id)
    {
        $users = $this->getDummyUsers();

        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Get list of study programs
     */
    private function getStudyPrograms()
    {
        return [
            'Teknik Informatika',
            'Sistem Informasi',
            'Teknik Komputer',
            'Ilmu Komputer',
            'Teknologi Informasi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Arsitektur',
            'Manajemen',
            'Akuntansi',
            'Ekonomi Pembangunan',
            'Hukum',
            'Kedokteran',
            'Farmasi',
            'Sastra Indonesia',
            'Sastra Inggris'
        ];
    }
}
