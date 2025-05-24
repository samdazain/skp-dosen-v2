<?php

namespace App\Controllers;

use App\Models\LecturerModel;

class LecturerController extends BaseController
{
    protected $lecturerModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->lecturerModel = new LecturerModel();
        helper($this->helpers);
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $search = $this->request->getGet('search');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 10;

        $result = $this->lecturerModel->getLecturers($search, $perPage, ($page - 1) * $perPage);
        $total = $result['total'];

        $pager = service('pager');
        $pager->setPath('lecturers');
        $pager->makeLinks($page, $perPage, $total);

        $lecturers = $result['lecturers'];
        foreach ($lecturers as &$lecturer) {
            if (!empty($lecturer['study_program'])) {
                $lecturer['study_program'] = $this->lecturerModel->formatStudyProgram($lecturer['study_program']);
            }
        }

        return view('lecturers/index', [
            'pageTitle' => 'Daftar Dosen | SKP Dosen',
            'lecturers' => $lecturers,
            'pager' => $pager,
            'search' => $search
        ]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('lecturers/create', [
            'pageTitle' => 'Tambah Dosen | SKP Dosen',
            'validation' => \Config\Services::validation(),
            'positions' => $this->lecturerModel->getPositions(),
            'studyPrograms' => $this->lecturerModel->getStudyPrograms()
        ]);
    }

    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $postData = $this->request->getPost();
        $position = $postData['position'] ?? '';

        $isLeadership = $this->lecturerModel->isLeadershipPosition($position);

        if ($isLeadership) {
            $rules = [
                'nip' => 'required|min_length[10]|max_length[30]|is_unique[lecturers.nip,id,{id}]',
                'name' => 'required|min_length[3]|max_length[100]',
                'position' => 'required|in_list[' . implode(',', $this->lecturerModel->getPositions()) . ']'
            ];
        } else {
            $rules = $this->lecturerModel->getValidationRules($postData, null, $isLeadership);
        }

        $messages = $this->lecturerModel->validationMessages;

        if ($isLeadership) {
            unset($messages['study_program']);
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Validation failed. Please check your input.');
        }

        $data = [
            'nip' => $postData['nip'],
            'name' => $postData['name'],
            'position' => $position,
        ];

        if (!$isLeadership) {
            $data['study_program'] = $postData['study_program'];
        } else {
            $data['study_program'] = null;
        }

        try {
            $insertId = $this->lecturerModel->insert($data);

            if (!$insertId) {
                $errors = $this->lecturerModel->errors();
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors)
                    ->with('error', 'Failed to add lecturer.');
            }

            return redirect()->to('lecturers')
                ->with('success', 'Data dosen berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error adding lecturer: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        return view('lecturers/edit', [
            'pageTitle' => 'Edit Dosen | SKP Dosen',
            'validation' => \Config\Services::validation(),
            'lecturer' => $lecturer,
            'positions' => $this->lecturerModel->getPositions(),
            'studyPrograms' => $this->lecturerModel->getStudyPrograms(),
            'isLeadershipPosition' => $this->lecturerModel->isLeadershipPosition($lecturer['position'])
        ]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        $postData = $this->request->getPost();
        $position = $postData['position'] ?? '';
        $isLeadership = $this->lecturerModel->isLeadershipPosition($position);

        // Pass the ID to getValidationRules to handle NIP uniqueness correctly
        $rules = $this->lecturerModel->getValidationRules($postData, $id);
        $messages = $this->lecturerModel->validationMessages;

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nip' => $postData['nip'],
            'name' => $postData['name'],
            'position' => $position,
            'study_program' => null
        ];

        if (!$isLeadership && !empty($postData['study_program'])) {
            $data['study_program'] = $postData['study_program'];
        }

        try {
            $this->lecturerModel->update($id, $data);
            return redirect()->to('lecturers')
                ->with('success', 'Data dosen berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        try {
            $this->lecturerModel->delete($id);
            return redirect()->to('lecturers')->with('success', 'Data dosen berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('lecturers')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
