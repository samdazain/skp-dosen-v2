<?php

namespace App\Controllers;

use App\Models\SemesterModel;
use CodeIgniter\API\ResponseTrait;

class SemesterController extends BaseController
{
    use ResponseTrait;

    protected $semesterModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->semesterModel = new SemesterModel();
        helper(['form', 'url']);
    }

    /**
     * Get all semesters
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $semesters = $this->semesterModel->getAllSemesters();
        $activeSemester = $this->semesterModel->getActiveSemester();

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => 'success',
                'data' => $semesters
            ]);
        }

        return view('semester/index', [
            'pageTitle' => 'Kelola Semester | SKP Dosen',
            'semesters' => $semesters,
            'activeSemester' => $activeSemester
        ]);
    }

    /**
     * Change active semester in session
     */
    public function change()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }
            return redirect()->to('/login');
        }

        // Get JSON input
        $json = $this->request->getJSON();
        $semesterId = $json->semester_id ?? $this->request->getVar('semester_id');

        if (!$semesterId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Semester ID is required']);
            }
            return redirect()->back()->with('error', 'Semester ID is required');
        }

        $semester = $this->semesterModel->getSemesterById($semesterId);

        if (!$semester) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Semester not found']);
            }
            return redirect()->back()->with('error', 'Semester not found');
        }

        try {
            // Set semester in session
            session()->set('activeSemesterId', $semesterId);
            session()->set('activeSemesterText', $this->semesterModel->formatSemester($semester));

            $redirectUrl = $json->redirect ?? $this->request->getVar('redirect') ?? base_url();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Semester aktif berhasil diubah',
                    'activeSemester' => $semester,
                    'formattedSemester' => $this->semesterModel->formatSemester($semester),
                    'redirectUrl' => $redirectUrl
                ]);
            }

            return redirect()->to($redirectUrl)->with('success', 'Semester aktif berhasil diubah');
        } catch (\Exception $e) {
            log_message('error', 'Error changing semester: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengubah semester']);
            }
            return redirect()->back()->with('error', 'Gagal mengubah semester');
        }
    }

    /**
     * Get current active semester
     */
    public function current()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->fail('Unauthorized', 401);
            }
            return redirect()->to('/login');
        }

        // Use the enhanced getCurrentSemester method which handles dynamic selection
        $semester = $this->semesterModel->getCurrentSemester();

        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => 'success',
                'data' => $semester,
                'formattedSemester' => $semester ? $this->semesterModel->formatSemester($semester) : null
            ]);
        }

        return $semester;
    }

    /**
     * Initialize semester for new login
     * This method can be called when user logs in to set appropriate semester
     */
    public function initializeSemester()
    {
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
            }
            return redirect()->to('/login');
        }

        // Clear any existing semester session data
        session()->remove('activeSemesterId');
        session()->remove('activeSemesterText');

        // Get the current semester (will auto-set based on date logic)
        $semester = $this->semesterModel->getCurrentSemester();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $semester,
                'formattedSemester' => $semester ? $this->semesterModel->formatSemester($semester) : null,
                'message' => 'Semester initialized based on current date'
            ]);
        }

        return redirect()->back()->with('success', 'Semester telah diatur berdasarkan tanggal saat ini');
    }

    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('semester/create', [
            'pageTitle' => 'Tambah Semester | SKP Dosen',
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $rules = [
            'year' => 'required|integer|min_length[4]|max_length[4]',
            'term' => 'required|in_list[1,2]'
        ];

        $messages = [
            'year' => [
                'required' => 'Tahun harus diisi',
                'integer' => 'Tahun harus berupa angka',
                'min_length' => 'Tahun harus 4 digit',
                'max_length' => 'Tahun harus 4 digit'
            ],
            'term' => [
                'required' => 'Semester harus dipilih',
                'in_list' => 'Semester harus Ganjil atau Genap'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', \Config\Services::validation()->getErrors());
        }

        $postData = $this->request->getPost();

        if ($this->semesterModel->semesterExists($postData['year'], $postData['term'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Semester ini sudah ada dalam database');
        }

        $data = [
            'year' => (int)$postData['year'],
            'term' => $postData['term']
        ];

        try {
            $this->semesterModel->insert($data);
            return redirect()->to('semester')
                ->with('success', 'Semester berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan semester: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            return redirect()->to('semester')->with('error', 'Semester tidak ditemukan');
        }

        return view('semester/edit', [
            'pageTitle' => 'Edit Semester | SKP Dosen',
            'semester' => $semester,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            return redirect()->to('semester')->with('error', 'Semester tidak ditemukan');
        }

        $rules = [
            'year' => 'required|integer|min_length[4]|max_length[4]',
            'term' => 'required|in_list[1,2]'
        ];

        $messages = [
            'year' => [
                'required' => 'Tahun harus diisi',
                'integer' => 'Tahun harus berupa angka',
                'min_length' => 'Tahun harus 4 digit',
                'max_length' => 'Tahun harus 4 digit'
            ],
            'term' => [
                'required' => 'Semester harus dipilih',
                'in_list' => 'Semester harus Ganjil atau Genap'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', \Config\Services::validation()->getErrors());
        }

        $postData = $this->request->getPost();

        if ($this->semesterModel->semesterExists($postData['year'], $postData['term'], $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Semester ini sudah ada dalam database');
        }

        $data = [
            'year' => (int)$postData['year'],
            'term' => $postData['term']
        ];

        try {
            $this->semesterModel->update($id, $data);

            // Update session if this is the active semester
            if (session()->get('activeSemesterId') == $id) {
                $updatedSemester = $this->semesterModel->find($id);
                session()->set('activeSemesterText', $this->semesterModel->formatSemester($updatedSemester));
            }

            return redirect()->to('semester')
                ->with('success', 'Semester berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui semester: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            return redirect()->to('semester')->with('error', 'Semester tidak ditemukan');
        }

        if ($this->semesterModel->isActiveSemester($id)) {
            return redirect()->to('semester')
                ->with('error', 'Tidak dapat menghapus semester yang sedang aktif');
        }

        try {
            $this->semesterModel->delete($id);
            return redirect()->to('semester')
                ->with('success', 'Semester berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('semester')
                ->with('error', 'Gagal menghapus semester: ' . $e->getMessage());
        }
    }

    public function setActive($id)
    {
        // Redirect to the change method for consistency
        return $this->change();
    }

    public function getCurrentDefault()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        $defaultTerm = ($currentMonth >= 8) ? '1' : '2';
        $defaultYear = ($currentMonth >= 8) ? $currentYear : $currentYear - 1;

        return $this->response->setJSON([
            'year' => $defaultYear,
            'term' => $defaultTerm
        ]);
    }
}
