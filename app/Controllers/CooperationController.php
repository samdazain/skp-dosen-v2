<?php

namespace App\Controllers;

use App\Models\CooperationModel;
use App\Models\SemesterModel;
use App\Models\ScoreModel;

class CooperationController extends BaseController
{
    protected $cooperationModel;
    protected $semesterModel;
    protected $scoreModel;

    protected $returnType = 'object';

    public function __construct()
    {
        $this->cooperationModel = new CooperationModel();
        $this->semesterModel = new SemesterModel();
        $this->scoreModel = new ScoreModel();
    }

    /**
     * Display list of lecturer cooperation data
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

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('dashboard')->with('error', 'Tidak ada semester aktif.');
        }

        // Initialize filters with default empty values
        $filters = [
            'position' => $this->request->getGet('position') ?? '',
            'study_program' => $this->request->getGet('study_program') ?? '',
            'level' => $this->request->getGet('level') ?? ''
        ];

        // Remove empty filter values for the actual filtering
        $activeFilters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        try {
            // Auto-populate cooperation data for all lecturers
            $addedCount = $this->cooperationModel->autoPopulateCooperationData($currentSemester['id']);

            // Refresh cooperation data (sync and recalculate)
            $this->cooperationModel->refreshCooperationData($currentSemester['id']);

            if ($addedCount > 0) {
                session()->setFlashdata('info', "Auto-populated {$addedCount} new cooperation records");
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception during cooperation data auto-population: ' . $e->getMessage());
            session()->setFlashdata('warning', 'Terjadi masalah saat memuat data kerja sama secara otomatis: ' . $e->getMessage());
        }

        try {
            // Get all lecturers with their cooperation data
            $lecturersData = $this->cooperationModel->getAllLecturersWithCooperation($currentSemester['id'], $activeFilters);

            // Get statistics
            $stats = $this->cooperationModel->getCooperationStats($currentSemester['id']);

            // Get all semesters for semester selector
            $semesters = $this->semesterModel->getAllSemesters();

            return view('cooperation/index', [
                'pageTitle' => 'Data Kerja Sama | SKP Dosen',
                'user' => $userData,
                'lecturersData' => $lecturersData,
                'stats' => $stats,
                'currentSemester' => $currentSemester,
                'semesters' => $semesters,
                'filters' => $filters, // Pass all filters including empty ones for form state
                'autoPopulationResult' => [
                    'added' => $addedCount ?? 0,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading cooperation data: ' . $e->getMessage());

            // Fallback: return view with empty data but show error
            return view('cooperation/index', [
                'pageTitle' => 'Data Kerja Sama | SKP Dosen',
                'user' => $userData,
                'lecturersData' => [],
                'stats' => [
                    'total_lecturers' => 0,
                    'level_counts' => [
                        'very_cooperative' => 0,
                        'cooperative' => 0,
                        'fair' => 0,
                        'not_cooperative' => 0
                    ],
                    'level_percentages' => [
                        'very_cooperative' => 0,
                        'cooperative' => 0,
                        'fair' => 0,
                        'not_cooperative' => 0
                    ]
                ],
                'currentSemester' => $currentSemester,
                'semesters' => $this->semesterModel->getAllSemesters(),
                'filters' => $filters, // Ensure filters are always passed
                'autoPopulationResult' => ['added' => 0]
            ]);
        }
    }

    /**
     * Update cooperation level for a lecturer
     */
    public function updateCooperationLevel()
    {
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturerId = $this->request->getPost('lecturer_id');
        $level = $this->request->getPost('level');

        if (!$lecturerId || !isset($level)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
            }
            return redirect()->to('cooperation')->with('error', 'Data tidak lengkap');
        }

        $lecturer = (new \App\Models\LecturerModel())->find($lecturerId);
        if (!$lecturer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Dosen tidak ditemukan']);
            }
            return redirect()->to('cooperation')->with('error', 'Dosen tidak ditemukan');
        }


        // Convert to array if it's an object
        if (is_object($lecturer)) {
            $lecturer = $lecturer->toArray();
        }

        // Check if user can update this lecturer's data
        helper('role');

        $lecturerStudyProgram = $lecturer['study_program'];

        // Debug: Tambahkan informasi study program ke response
        $debugInfo = [
            'lecturer_study_program' =>  $lecturer['study_program'],
            'user_role' => session()->get('user_role'),
            'user_study_program' => session()->get('user_study_program'),
            'can_update' => can_update_lecturer_score($lecturerStudyProgram)
        ];

        // Modifikasi fungsi can_update_lecturer_score agar hanya mengizinkan update jika:
        // - User memiliki role 'kaprodi'
        // - User memiliki study program yang sama dengan lecturer
        if (!can_update_lecturer_score($lecturerStudyProgram)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah data dosen ini',
                    'debug' => $debugInfo
                ]);
            }
            return redirect()->to('cooperation')->with('error', 'Anda tidak memiliki akses untuk mengubah data dosen ini');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada semester aktif']);
            }
            return redirect()->to('cooperation')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $result = $this->cooperationModel->updateCooperationLevel(
                $lecturerId,
                $currentSemester['id'],
                $level,
                session()->get('user_id')
            );

            // Get the updated record to return the new score
            $updatedRecord = $this->cooperationModel->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $currentSemester['id']
            ])->first();

            $newScore = $updatedRecord ? $updatedRecord['score'] : $this->cooperationModel->calculateCooperationScore($level);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Tingkat kerja sama berhasil diperbarui',
                    'new_score' => $newScore,
                    'level' => $level,
                    'debug' => array_merge($debugInfo, ['action' => 'update_successful'])
                ]);
            }

            return redirect()->to('cooperation')->with('success', 'Tingkat kerja sama berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', 'Error updating cooperation level: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kesalahan: ' . $e->getMessage(),
                    'debug' => array_merge($debugInfo, ['error' => $e->getMessage()])
                ]);
            }
            return redirect()->to('cooperation')->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export cooperation data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('cooperation')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'level' => $this->request->getGet('level')
            ]);

            $this->cooperationModel->getAllLecturersWithCooperation($currentSemester['id'], $filters);
            return redirect()->to('cooperation')->with('success', 'Data kerja sama berhasil diekspor ke Excel');
        } catch (\Exception $e) {
            return redirect()->to('cooperation')->with('error', 'Gagal mengekspor data');
        }
    }

    /**
     * Export cooperation data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('cooperation')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'level' => $this->request->getGet('level')
            ]);

            $this->cooperationModel->getAllLecturersWithCooperation($currentSemester['id'], $filters);
            return redirect()->to('cooperation')->with('success', 'Data kerja sama berhasil diekspor ke PDF');
        } catch (\Exception $e) {
            return redirect()->to('cooperation')->with('error', 'Gagal mengekspor data');
        }
    }
}
