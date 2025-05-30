<?php

namespace App\Controllers;

use App\Models\CommitmentModel;
use App\Models\SemesterModel;
use App\Models\ScoreModel;

class CommitmentController extends BaseController
{
    protected $commitmentModel;
    protected $semesterModel;
    protected $scoreModel;

    public function __construct()
    {
        $this->commitmentModel = new CommitmentModel();
        $this->semesterModel = new SemesterModel();
        $this->scoreModel = new ScoreModel();
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

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('dashboard')->with('error', 'Tidak ada semester aktif.');
        }

        // Get lecturer count for debugging
        $lecturerModel = new \App\Models\LecturerModel();
        $totalLecturers = $lecturerModel->countAll();

        try {
            // Auto-populate commitment data for all lecturers
            $addedCount = $this->commitmentModel->autoPopulateCommitmentData($currentSemester['id']);

            // Refresh commitment data (sync and recalculate)
            $this->commitmentModel->refreshCommitmentData($currentSemester['id']);

            if ($addedCount > 0) {
                session()->setFlashdata('info', "Auto-populated {$addedCount} new commitment records");
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception during commitment data auto-population: ' . $e->getMessage());
            session()->setFlashdata('warning', 'Terjadi masalah saat memuat data komitmen secara otomatis: ' . $e->getMessage());
        }

        $filters = array_filter([
            'position' => $this->request->getGet('position'),
            'study_program' => $this->request->getGet('study_program'),
            'competence' => $this->request->getGet('competence'),
            'tridharma' => $this->request->getGet('tridharma')
        ]);

        try {
            // Get all lecturers with their commitment data
            $lecturersData = $this->commitmentModel->getAllLecturersWithCommitment($currentSemester['id'], $filters);

            // Get statistics
            $stats = $this->commitmentModel->getCommitmentStats($currentSemester['id']);

            // Get all semesters for semester selector
            $semesters = $this->semesterModel->getAllSemesters();

            return view('commitment/index', [
                'pageTitle' => 'Data Komitmen | SKP Dosen',
                'user' => $userData,
                'lecturersData' => $lecturersData,
                'stats' => $stats,
                'currentSemester' => $currentSemester,
                'semesters' => $semesters,
                'filters' => $filters,
                'autoPopulationResult' => [
                    'added' => $addedCount ?? 0,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading commitment data: ' . $e->getMessage());

            // Fallback: return view with empty data but show error
            return view('commitment/index', [
                'pageTitle' => 'Data Komitmen | SKP Dosen',
                'user' => $userData,
                'lecturersData' => [],
                'stats' => array_fill_keys([
                    'total_lecturers',
                    'active_competence',
                    'inactive_competence',
                    'pass_tri_dharma',
                    'fail_tri_dharma',
                    'active_competence_percentage',
                    'pass_tri_dharma_percentage'
                ], 0),
                'currentSemester' => $currentSemester,
                'semesters' => $this->semesterModel->getAllSemesters(),
                'filters' => $filters,
                'autoPopulationResult' => ['added' => 0]
            ]);
        }
    }

    public function updateCompetency()
    {
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturerId = $this->request->getPost('lecturer_id');
        $status = $this->request->getPost('status');

        if (!$lecturerId || !isset($status)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
            }
            return redirect()->to('commitment')->with('error', 'Data tidak lengkap');
        }

        $lecturer = (new \App\Models\LecturerModel())->find($lecturerId);
        if (!$lecturer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Dosen tidak ditemukan']);
            }
            return redirect()->to('commitment')->with('error', 'Dosen tidak ditemukan');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada semester aktif']);
            }
            return redirect()->to('commitment')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $competenceStatus = in_array($status, ['true', '1', 1]) ? 'active' : 'inactive';
            $result = $this->commitmentModel->updateCompetency(
                $lecturerId,
                $currentSemester['id'],
                $competenceStatus,
                session()->get('user_id')
            );

            // Get the updated record to return the new score
            $updatedRecord = $this->commitmentModel->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $currentSemester['id']
            ])->first();

            $newScore = $updatedRecord ? $updatedRecord['score'] : 0;

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => $result,
                    'message' => $result ? 'Status kompetensi berhasil diperbarui' : 'Gagal memperbarui status kompetensi',
                    'new_score' => $newScore,
                    'competence' => $competenceStatus
                ]);
            }

            return redirect()->to('commitment')->with(
                $result ? 'success' : 'error',
                $result ? 'Status kompetensi berhasil diperbarui' : 'Gagal memperbarui status kompetensi'
            );
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
            }
            return redirect()->to('commitment')->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function updateTriDharma()
    {
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturerId = $this->request->getPost('lecturer_id');
        $status = $this->request->getPost('status');

        if (!$lecturerId || !isset($status)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
            }
            return redirect()->to('commitment')->with('error', 'Data tidak lengkap');
        }

        $lecturer = (new \App\Models\LecturerModel())->find($lecturerId);
        if (!$lecturer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Dosen tidak ditemukan']);
            }
            return redirect()->to('commitment')->with('error', 'Dosen tidak ditemukan');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada semester aktif']);
            }
            return redirect()->to('commitment')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $triDharmaStatus = in_array($status, ['true', '1', 1]) ? 1 : 0;
            $result = $this->commitmentModel->updateTriDharma(
                $lecturerId,
                $currentSemester['id'],
                $triDharmaStatus,
                session()->get('user_id')
            );

            // Get the updated record to return the new score
            $updatedRecord = $this->commitmentModel->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $currentSemester['id']
            ])->first();

            $newScore = $updatedRecord ? $updatedRecord['score'] : 0;

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => $result,
                    'message' => $result ? 'Status Tri Dharma berhasil diperbarui' : 'Gagal memperbarui status Tri Dharma',
                    'new_score' => $newScore,
                    'tridharma_pass' => $triDharmaStatus
                ]);
            }

            return redirect()->to('commitment')->with(
                $result ? 'success' : 'error',
                $result ? 'Status Tri Dharma berhasil diperbarui' : 'Gagal memperbarui status Tri Dharma'
            );
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kesalahan: ' . $e->getMessage()]);
            }
            return redirect()->to('commitment')->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('commitment')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'competence' => $this->request->getGet('competence'),
                'tridharma' => $this->request->getGet('tridharma')
            ]);

            $this->commitmentModel->getAllLecturersWithCommitment($currentSemester['id'], $filters);
            return redirect()->to('commitment')->with('success', 'Data komitmen berhasil diekspor ke Excel');
        } catch (\Exception $e) {
            return redirect()->to('commitment')->with('error', 'Gagal mengekspor data');
        }
    }

    public function exportPdf()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('commitment')->with('error', 'Tidak ada semester aktif');
        }

        try {
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'competence' => $this->request->getGet('competence'),
                'tridharma' => $this->request->getGet('tridharma')
            ]);

            $this->commitmentModel->getAllLecturersWithCommitment($currentSemester['id'], $filters);
            return redirect()->to('commitment')->with('success', 'Data komitmen berhasil diekspor ke PDF');
        } catch (\Exception $e) {
            return redirect()->to('commitment')->with('error', 'Gagal mengekspor data');
        }
    }

    public function bulkUpdate()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $updates = $this->request->getJSON(true);
        if (empty($updates) || !is_array($updates)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data format']);
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return $this->response->setJSON(['success' => false, 'message' => 'No active semester']);
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($updates as $update) {
            try {
                $lecturerId = $update['lecturer_id'] ?? null;
                $field = $update['field'] ?? null;
                $value = $update['value'] ?? null;

                if (!$lecturerId || !$field) {
                    $errorCount++;
                    continue;
                }

                $method = $field === 'competence' ? 'updateCompetency' : ($field === 'tridharma' ? 'updateTriDharma' : null);
                if ($method && $this->commitmentModel->{$method}($lecturerId, $currentSemester['id'], $value, session()->get('user_id'))) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        return $this->response->setJSON([
            'success' => $successCount > 0,
            'message' => "Updated: {$successCount}, Errors: {$errorCount}"
        ]);
    }
}
