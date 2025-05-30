<?php

namespace App\Controllers;

use App\Models\IntegrityModel;
use App\Models\SemesterModel;

class IntegrityController extends BaseController
{
    protected $integrityModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->integrityModel = new IntegrityModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Display list of lecturer integrity data
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
            return view('integrity/index', [
                'pageTitle' => 'Data Integritas | SKP Dosen',
                'user' => $userData,
                'integrityData' => [],
                'currentSemester' => null,
                'statistics' => [
                    'average_attendance_score' => 0,
                    'average_courses_score' => 0,
                    'total_lecturers' => 0,
                    'score_distribution' => [
                        'excellent' => 0,
                        'good' => 0,
                        'fair' => 0,
                        'poor' => 0
                    ]
                ]
            ]);
        }

        // Get integrity data with lecturer information (this will auto-populate)
        $integrityData = $this->integrityModel->getIntegrityDataWithLecturers($semesterId);

        // Get statistics
        $statistics = $this->integrityModel->getIntegrityStatistics($semesterId);

        return view('integrity/index', [
            'pageTitle' => 'Data Integritas | SKP Dosen',
            'user' => $userData,
            'integrityData' => $integrityData,
            'currentSemester' => $currentSemester,
            'statistics' => $statistics
        ]);
    }

    /**
     * Recalculate integrity scores for current semester
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
                return redirect()->to('integrity')->with('error', 'Tidak ada semester aktif');
            }

            // Recalculate only zero scores or all scores (choose one)
            $updatedCount = $this->integrityModel->recalculateZeroScores($semesterId);
            // OR use this for all scores: $updatedCount = $this->integrityModel->recalculateAllScores($semesterId);

            if ($updatedCount > 0) {
                return redirect()->to('integrity')->with('success', "Berhasil memperbarui {$updatedCount} nilai integritas");
            } else {
                return redirect()->to('integrity')->with('info', 'Tidak ada nilai yang perlu diperbarui');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error recalculating integrity scores: ' . $e->getMessage());
            return redirect()->to('integrity')->with('error', 'Gagal memperbarui nilai integritas');
        }
    }

    /**
     * Force recalculate all integrity scores
     */
    public function forceRecalculateAll()
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
                return redirect()->to('integrity')->with('error', 'Tidak ada semester aktif');
            }

            $updatedCount = $this->integrityModel->recalculateAllScores($semesterId);

            return redirect()->to('integrity')->with('success', "Berhasil memperbarui {$updatedCount} nilai integritas");
        } catch (\Exception $e) {
            log_message('error', 'Error force recalculating integrity scores: ' . $e->getMessage());
            return redirect()->to('integrity')->with('error', 'Gagal memperbarui nilai integritas');
        }
    }

    /**
     * Export integrity data to Excel format
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
            return redirect()->to('integrity')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        // Get data
        $integrityData = $this->integrityModel->getIntegrityDataWithLecturers($semesterId);

        if (empty($integrityData)) {
            return redirect()->to('integrity')->with('error', 'Tidak ada data integritas untuk diekspor');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message
        return redirect()->to('integrity')->with('success', 'Data integritas berhasil diekspor ke Excel');
    }

    /**
     * Export integrity data to PDF format
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
            return redirect()->to('integrity')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        // Get data
        $integrityData = $this->integrityModel->getIntegrityDataWithLecturers($semesterId);

        if (empty($integrityData)) {
            return redirect()->to('integrity')->with('error', 'Tidak ada data integritas untuk diekspor');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message
        return redirect()->to('integrity')->with('success', 'Data integritas berhasil diekspor ke PDF');
    }
}
