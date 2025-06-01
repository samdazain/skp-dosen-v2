<?php

namespace App\Controllers;

use App\Models\SKPModel;
use App\Models\SemesterModel;

class SKPController extends BaseController
{
    protected $skpModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->skpModel = new SKPModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Display SKP data with lecturer information
     * Automatically populates and calculates scores on every page load
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

        // Get current semester
        $currentSemester = $this->semesterModel->getCurrentSemester();
        $semesterId = $currentSemester ? $currentSemester['id'] : null;

        // Get filters from request
        $filters = array_filter([
            'position' => $this->request->getGet('position'),
            'study_program' => $this->request->getGet('study_program'),
            'skp_category' => $this->request->getGet('skp_category')
        ]);

        if (!$semesterId) {
            return view('skp/index', [
                'pageTitle' => 'Data Master SKP | SKP Dosen',
                'user' => $userData,
                'skpData' => [],
                'currentSemester' => null,
                'allSemesters' => $this->semesterModel->getAllSemesters(),
                'filters' => $filters,
                'statistics' => $this->skpModel->getEmptyStatistics(),
                'calculationResult' => ['updated' => 0]
            ]);
        }

        try {
            // Auto-populate SKP data for all lecturers
            $addedCount = $this->skpModel->autoPopulateSKPData($semesterId);

            // Automatically recalculate ALL SKP scores on every page load
            $updatedCount = $this->skpModel->recalculateAllSKPScores($semesterId);

            // Log the automatic calculation
            if ($updatedCount > 0) {
                log_message('info', "SKP auto-calculation: Updated {$updatedCount} scores for semester {$semesterId}");
            }

            // Get SKP data with lecturer information and real-time component scores
            $skpData = $this->skpModel->getSKPDataWithLecturers($semesterId, $filters);

            // Get statistics based on real-time data
            $statistics = $this->skpModel->getSKPStatistics($semesterId);

            // Get component scores summary for additional insights
            $componentSummary = $this->skpModel->getComponentScoresSummary($semesterId);

            // Get all semesters for selector
            $allSemesters = $this->semesterModel->getAllSemesters();

            return view('skp/index', [
                'pageTitle' => 'Data Master SKP | SKP Dosen',
                'user' => $userData,
                'skpData' => $skpData,
                'currentSemester' => $currentSemester,
                'allSemesters' => $allSemesters,
                'filters' => $filters,
                'statistics' => $statistics,
                'componentSummary' => $componentSummary,
                'calculationResult' => [
                    'added' => $addedCount ?? 0,
                    'updated' => $updatedCount,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in SKP auto-calculation: ' . $e->getMessage());

            // If auto-calculation fails, still show the page but with warning
            try {
                $skpData = $this->skpModel->getSKPDataWithLecturers($semesterId, $filters);
                $statistics = $this->skpModel->getSKPStatistics($semesterId);
                $componentSummary = $this->skpModel->getComponentScoresSummary($semesterId);
            } catch (\Exception $e2) {
                log_message('error', 'Error getting SKP data after calculation failure: ' . $e2->getMessage());
                $skpData = [];
                $statistics = $this->skpModel->getEmptyStatistics();
                $componentSummary = [];
            }

            session()->setFlashdata('warning', 'Terjadi masalah saat menghitung ulang nilai SKP secara otomatis: ' . $e->getMessage());

            return view('skp/index', [
                'pageTitle' => 'Data Master SKP | SKP Dosen',
                'user' => $userData,
                'skpData' => $skpData,
                'currentSemester' => $currentSemester,
                'allSemesters' => $this->semesterModel->getAllSemesters(),
                'filters' => $filters,
                'statistics' => $statistics,
                'componentSummary' => $componentSummary ?? [],
                'calculationResult' => ['added' => 0, 'updated' => 0]
            ]);
        }
    }

    /**
     * Switch semester and redirect to SKP data
     */
    public function switchSemester()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $semesterId = $this->request->getPost('semester_id');

        if (!$semesterId) {
            return redirect()->to('skp')->with('error', 'Semester tidak valid');
        }

        // Verify semester exists
        $semester = $this->semesterModel->find($semesterId);
        if (!$semester) {
            return redirect()->to('skp')->with('error', 'Semester tidak ditemukan');
        }

        // Convert to array if it's an object
        if (is_object($semester)) {
            $semester = $semester->toArray();
        }

        // Set as current semester (you might want to store this in session)
        session()->set('selected_semester_id', $semesterId);

        return redirect()->to('skp')->with('success', "Beralih ke semester {$semester['year']}/{$semester['term']}");
    }

    /**
     * Manually recalculate SKP scores (admin function)
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
                return redirect()->to('skp')->with('error', 'Tidak ada semester aktif');
            }

            $updatedCount = $this->skpModel->recalculateAllSKPScores($semesterId);

            return redirect()->to('skp')->with('success', "Berhasil memperbarui {$updatedCount} nilai SKP secara manual");
        } catch (\Exception $e) {
            log_message('error', 'Error manually recalculating SKP scores: ' . $e->getMessage());
            return redirect()->to('skp')->with('error', 'Gagal memperbarui nilai SKP secara manual: ' . $e->getMessage());
        }
    }

    /**
     * Export SKP data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Get current semester
            $currentSemester = $this->semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;

            if (!$semesterId) {
                return redirect()->to('skp')->with('error', 'Tidak ada semester aktif untuk diekspor');
            }

            // Get filters from request
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'skp_category' => $this->request->getGet('skp_category')
            ]);

            // Get SKP data with filters
            $skpData = $this->skpModel->getSKPDataWithLecturers($semesterId, $filters);

            if (empty($skpData)) {
                return redirect()->to('skp')->with('error', 'Tidak ada data SKP untuk diekspor');
            }

            // Use SKPExcelService to export
            $excelService = new \App\Services\SKPExcelService();
            $excelService->exportSKPData($skpData, $currentSemester, $filters);
        } catch (\Exception $e) {
            log_message('error', 'SKP Excel export error: ' . $e->getMessage());
            return redirect()->to('skp')->with('error', 'Gagal mengekspor data SKP ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export SKP data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            // Get current semester
            $currentSemester = $this->semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;

            if (!$semesterId) {
                return redirect()->to('skp')->with('error', 'Tidak ada semester aktif untuk diekspor');
            }

            // Get filters from request
            $filters = array_filter([
                'position' => $this->request->getGet('position'),
                'study_program' => $this->request->getGet('study_program'),
                'skp_category' => $this->request->getGet('skp_category')
            ]);

            // Get SKP data with filters
            $skpData = $this->skpModel->getSKPDataWithLecturers($semesterId, $filters);

            if (empty($skpData)) {
                return redirect()->to('skp')->with('error', 'Tidak ada data SKP untuk diekspor');
            }

            // Use SKPPdfService to export
            $pdfService = new \App\Services\SKPPdfService();
            $pdfService->exportSKPData($skpData, $currentSemester, $filters);
        } catch (\Exception $e) {
            log_message('error', 'SKP PDF export error: ' . $e->getMessage());
            return redirect()->to('skp')->with('error', 'Gagal mengekspor data SKP ke PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed component breakdown for a specific lecturer (AJAX)
     */
    public function getLecturerDetails($lecturerId = null)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$lecturerId || !$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        try {
            $currentSemester = $this->semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;

            if (!$semesterId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No active semester']);
            }

            $details = $this->skpModel->getLecturerComponentDetails($lecturerId, $semesterId);

            if (!$details) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Lecturer not found']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $details
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting lecturer details: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to get lecturer details']);
        }
    }
}
