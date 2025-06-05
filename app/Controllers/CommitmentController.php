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

        // Load role helper for permissions
        helper('role');

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return redirect()->to('dashboard')->with('error', 'Tidak ada semester aktif.');
        }

        try {
            // Auto-populate and recalculate scores (similar to DisciplineController)
            $addedCount = $this->commitmentModel->autoPopulateCommitmentData($currentSemester['id']);
            $updatedCount = $this->commitmentModel->recalculateAllScores($currentSemester['id']);

            // Log the automatic calculation
            if ($updatedCount > 0) {
                log_message('info', "Commitment auto-calculation: Updated {$updatedCount} scores for semester {$currentSemester['id']}");
            }

            if ($addedCount > 0) {
                session()->setFlashdata('info', "Auto-populated {$addedCount} new commitment records");
            }

            if ($updatedCount > 0) {
                session()->setFlashdata('success', "Auto-recalculated {$updatedCount} commitment scores");
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception during commitment data auto-calculation: ' . $e->getMessage());
            session()->setFlashdata('warning', 'Terjadi masalah saat menghitung ulang skor komitmen secara otomatis: ' . $e->getMessage());
        }

        // Get user filters from request
        $userFilters = array_filter([
            'position' => $this->request->getGet('position'),
            'study_program' => $this->request->getGet('study_program'),
            'competence' => $this->request->getGet('competence'),
            'tridharma' => $this->request->getGet('tridharma')
        ]);

        // Apply role-based study program filtering
        $filters = apply_study_program_filter($userFilters);

        try {
            // Get all lecturers with their commitment data (filtered by role)
            $lecturersData = $this->commitmentModel->getAllLecturersWithCommitment($currentSemester['id'], $filters);

            // Get statistics
            $stats = $this->commitmentModel->getCommitmentStats($currentSemester['id']);

            // Get all semesters for semester selector
            $semesters = $this->semesterModel->getAllSemesters();

            // Check if user is restricted to specific study program
            $isStudyProgramRestricted = !can_manage_all_lecturers();
            $userStudyProgram = get_user_study_program_filter();

            return view('commitment/index', [
                'pageTitle' => 'Data Komitmen | SKP Dosen',
                'user' => $userData,
                'lecturersData' => $lecturersData,
                'stats' => $stats,
                'currentSemester' => $currentSemester,
                'semesters' => $semesters,
                'filters' => $userFilters, // Original user filters for form state
                'autoPopulationResult' => [
                    'added' => $addedCount ?? 0,
                    'updated' => $updatedCount ?? 0,
                    'timestamp' => date('Y-m-d H:i:s')
                ],
                'isStudyProgramRestricted' => $isStudyProgramRestricted,
                'userStudyProgram' => $userStudyProgram
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
                'autoPopulationResult' => ['added' => 0, 'updated' => 0]
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

        // Check if user can update this lecturer's data
        helper('role');
        if (!can_update_lecturer_score($lecturer['study_program'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah data dosen ini']);
            }
            return redirect()->to('commitment')->with('error', 'Anda tidak memiliki akses untuk mengubah data dosen ini');
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

            if (!$result) {
                throw new \Exception('Failed to update competency status');
            }

            // Get the updated record to return the new score
            $updatedRecord = $this->commitmentModel->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $currentSemester['id']
            ])->first();

            if (!$updatedRecord) {
                throw new \Exception('Failed to retrieve updated record');
            }

            $newScore = $updatedRecord['score'];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status kompetensi berhasil diperbarui',
                    'new_score' => $newScore,
                    'competence' => $competenceStatus
                ]);
            }

            return redirect()->to('commitment')->with('success', 'Status kompetensi berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', 'Error updating competency: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kesalahan sistem: ' . $e->getMessage()]);
            }
            return redirect()->to('commitment')->with('error', 'Kesalahan sistem: ' . $e->getMessage());
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

        // Check if user can update this lecturer's data
        helper('role');
        if (!can_update_lecturer_score($lecturer['study_program'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Anda tidak memiliki akses untuk mengubah data dosen ini']);
            }
            return redirect()->to('commitment')->with('error', 'Anda tidak memiliki akses untuk mengubah data dosen ini');
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

            if (!$result) {
                throw new \Exception('Failed to update Tri Dharma status');
            }

            // Get the updated record to return the new score
            $updatedRecord = $this->commitmentModel->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $currentSemester['id']
            ])->first();

            if (!$updatedRecord) {
                throw new \Exception('Failed to retrieve updated record');
            }

            $newScore = $updatedRecord['score'];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status Tri Dharma berhasil diperbarui',
                    'new_score' => $newScore,
                    'tridharma_pass' => $triDharmaStatus
                ]);
            }

            return redirect()->to('commitment')->with('success', 'Status Tri Dharma berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', 'Error updating Tri Dharma: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kesalahan sistem: ' . $e->getMessage()]);
            }
            return redirect()->to('commitment')->with('error', 'Kesalahan sistem: ' . $e->getMessage());
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

    /**
     * Manually recalculate commitment scores (admin function)
     */
    public function recalculateScores()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $currentSemester = $this->semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;

            if (!$semesterId) {
                return redirect()->to('commitment')->with('error', 'Tidak ada semester aktif');
            }

            $updatedCount = $this->commitmentModel->recalculateAllScores($semesterId);

            return redirect()->to('commitment')->with('success', "Berhasil memperbarui {$updatedCount} nilai komitmen secara manual");
        } catch (\Exception $e) {
            log_message('error', 'Error manually recalculating commitment scores: ' . $e->getMessage());
            return redirect()->to('commitment')->with('error', 'Gagal memperbarui nilai komitmen secara manual: ' . $e->getMessage());
        }
    }
}
