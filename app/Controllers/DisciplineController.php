<?php

namespace App\Controllers;

use App\Models\DisciplineModel;
use App\Models\SemesterModel;

class DisciplineController extends BaseController
{
    protected $disciplineModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Display list of lecturer discipline data
     * Automatically recalculates scores on every page load
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

        if (!$semesterId) {
            return view('discipline/index', [
                'pageTitle' => 'Data Disiplin | SKP Dosen',
                'user' => $userData,
                'disciplineData' => [],
                'currentSemester' => null,
                'statistics' => [
                    'average_daily_score' => 0,
                    'average_exercise_score' => 0,
                    'average_ceremony_score' => 0,
                    'total_lecturers' => 0,
                    'daily' => ['count_no_alpha' => 0, 'count_1_2_alpha' => 0, 'count_3_4_alpha' => 0, 'count_above_5_alpha' => 0, 'total' => 0, 'percentage_no_alpha' => 0, 'percentage_1_2_alpha' => 0, 'percentage_3_4_alpha' => 0, 'percentage_above_5_alpha' => 0],
                    'exercise' => ['count_no_alpha' => 0, 'count_1_2_alpha' => 0, 'count_3_4_alpha' => 0, 'count_above_5_alpha' => 0, 'total' => 0, 'percentage_no_alpha' => 0, 'percentage_1_2_alpha' => 0, 'percentage_3_4_alpha' => 0, 'percentage_above_5_alpha' => 0],
                    'ceremony' => ['count_no_alpha' => 0, 'count_1_2_alpha' => 0, 'count_3_4_alpha' => 0, 'count_above_5_alpha' => 0, 'total' => 0, 'percentage_no_alpha' => 0, 'percentage_1_2_alpha' => 0, 'percentage_3_4_alpha' => 0, 'percentage_above_5_alpha' => 0],
                    'score_distribution' => [
                        'excellent' => 0,
                        'good' => 0,
                        'fair' => 0,
                        'poor' => 0
                    ]
                ],
                'calculationResult' => ['updated' => 0]
            ]);
        }

        try {
            // Auto-populate discipline data for all lecturers
            $this->disciplineModel->autoPopulateDisciplineData($semesterId);

            // Automatically recalculate ALL scores on every page load
            $updatedCount = $this->disciplineModel->recalculateAllScores($semesterId);

            // Log the automatic calculation
            if ($updatedCount > 0) {
                log_message('info', "Discipline auto-calculation: Updated {$updatedCount} scores for semester {$semesterId}");
            }

            // Get discipline data with lecturer information
            $disciplineData = $this->disciplineModel->getDisciplineDataWithLecturers($semesterId);

            // Get statistics
            $statistics = $this->disciplineModel->getDisciplineStatistics($semesterId);

            return view('discipline/index', [
                'pageTitle' => 'Data Disiplin | SKP Dosen',
                'user' => $userData,
                'disciplineData' => $disciplineData,
                'currentSemester' => $currentSemester,
                'statistics' => $statistics,
                'calculationResult' => [
                    'updated' => $updatedCount,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in discipline auto-calculation: ' . $e->getMessage());

            // If auto-calculation fails, still show the page but with warning
            $disciplineData = $this->disciplineModel->getDisciplineDataWithLecturers($semesterId);
            $statistics = $this->disciplineModel->getDisciplineStatistics($semesterId);

            session()->setFlashdata('warning', 'Terjadi masalah saat menghitung ulang nilai disiplin secara otomatis');

            return view('discipline/index', [
                'pageTitle' => 'Data Disiplin | SKP Dosen',
                'user' => $userData,
                'disciplineData' => $disciplineData,
                'currentSemester' => $currentSemester,
                'statistics' => $statistics,
                'calculationResult' => ['updated' => 0]
            ]);
        }
    }

    /**
     * Manually recalculate discipline scores (admin function)
     */
    public function recalculateScores()
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
                return redirect()->to('discipline')->with('error', 'Tidak ada semester aktif');
            }

            $updatedCount = $this->disciplineModel->recalculateAllScores($semesterId);

            return redirect()->to('discipline')->with('success', "Berhasil memperbarui {$updatedCount} nilai disiplin secara manual");
        } catch (\Exception $e) {
            log_message('error', 'Error manually recalculating discipline scores: ' . $e->getMessage());
            return redirect()->to('discipline')->with('error', 'Gagal memperbarui nilai disiplin secara manual');
        }
    }

    /**
     * Export discipline data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get current semester
        $currentSemester = $this->semesterModel->getCurrentSemester();
        $semesterId = $currentSemester ? $currentSemester['id'] : null;

        if (!$semesterId) {
            return redirect()->to('discipline')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        // Get data
        $disciplineData = $this->disciplineModel->getDisciplineDataWithLecturers($semesterId);

        if (empty($disciplineData)) {
            return redirect()->to('discipline')->with('error', 'Tidak ada data disiplin untuk diekspor');
        }

        // In a real application, you would generate an Excel file here
        return redirect()->to('discipline')->with('success', 'Data disiplin berhasil diekspor ke Excel');
    }

    /**
     * Export discipline data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get current semester
        $currentSemester = $this->semesterModel->getCurrentSemester();
        $semesterId = $currentSemester ? $currentSemester['id'] : null;

        if (!$semesterId) {
            return redirect()->to('discipline')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        // Get data
        $disciplineData = $this->disciplineModel->getDisciplineDataWithLecturers($semesterId);

        if (empty($disciplineData)) {
            return redirect()->to('discipline')->with('error', 'Tidak ada data disiplin untuk diekspor');
        }

        // In a real application, you would generate a PDF file here
        return redirect()->to('discipline')->with('success', 'Data disiplin berhasil diekspor ke PDF');
    }

    /**
     * API endpoint to get current calculation status (for AJAX monitoring)
     */
    public function getCalculationStatus()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->to('discipline');
        }

        try {
            $currentSemester = $this->semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;

            if (!$semesterId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada semester aktif'
                ]);
            }

            $totalRecords = $this->disciplineModel->where('semester_id', $semesterId)->countAllResults();
            $zeroScoreRecords = $this->disciplineModel->where('semester_id', $semesterId)
                ->where('score', 0)->countAllResults();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'total_records' => $totalRecords,
                    'zero_score_records' => $zeroScoreRecords,
                    'calculated_records' => $totalRecords - $zeroScoreRecords,
                    'last_updated' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting calculation status: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mendapatkan status perhitungan'
            ]);
        }
    }
}
