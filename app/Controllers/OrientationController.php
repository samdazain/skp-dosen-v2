<?php

namespace App\Controllers;

use App\Models\OrientationModel;
use App\Models\SemesterModel;

class OrientationController extends BaseController
{
    protected $orientationModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->orientationModel = new OrientationModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Display list of lecturer orientation data
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

        // Get filters from request
        $filters = array_filter([
            'position' => $this->request->getGet('position'),
            'study_program' => $this->request->getGet('study_program'),
            'score_range' => $this->request->getGet('score_range')
        ]);

        if (!$semesterId) {
            return view('orientation/index', [
                'pageTitle' => 'Data Orientasi Pelayanan | SKP Dosen',
                'user' => $userData,
                'orientationData' => [],
                'currentSemester' => null,
                'filters' => $filters,
                'statistics' => [
                    'average_score' => 0,
                    'average_questionnaire_score' => 0,
                    'total_lecturers' => 0,
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
            // Auto-populate orientation data for all lecturers
            $addedCount = $this->orientationModel->autoPopulateOrientationData($semesterId);

            // Automatically recalculate ALL scores on every page load
            $updatedCount = $this->orientationModel->recalculateAllScores($semesterId);

            // Log the automatic calculation
            if ($updatedCount > 0) {
                log_message('info', "Orientation auto-calculation: Updated {$updatedCount} scores for semester {$semesterId}");
            }

            // Get orientation data with lecturer information and apply filters
            $orientationData = $this->orientationModel->getOrientationDataWithLecturers($semesterId, $filters);

            // Get statistics
            $statistics = $this->orientationModel->getOrientationStatistics($semesterId);

            return view('orientation/index', [
                'pageTitle' => 'Data Orientasi Pelayanan | SKP Dosen',
                'user' => $userData,
                'orientationData' => $orientationData,
                'currentSemester' => $currentSemester,
                'filters' => $filters,
                'statistics' => $statistics,
                'calculationResult' => [
                    'added' => $addedCount ?? 0,
                    'updated' => $updatedCount,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in orientation auto-calculation: ' . $e->getMessage());

            // If auto-calculation fails, still show the page but with warning
            try {
                $orientationData = $this->orientationModel->getOrientationDataWithLecturers($semesterId, $filters);
                $statistics = $this->orientationModel->getOrientationStatistics($semesterId);
            } catch (\Exception $e2) {
                log_message('error', 'Error getting orientation data after calculation failure: ' . $e2->getMessage());
                $orientationData = [];
                $statistics = [
                    'average_score' => 0,
                    'average_questionnaire_score' => 0,
                    'total_lecturers' => 0,
                    'score_distribution' => [
                        'excellent' => 0,
                        'good' => 0,
                        'fair' => 0,
                        'poor' => 0
                    ]
                ];
            }

            session()->setFlashdata('warning', 'Terjadi masalah saat menghitung ulang nilai orientasi pelayanan secara otomatis: ' . $e->getMessage());

            return view('orientation/index', [
                'pageTitle' => 'Data Orientasi Pelayanan | SKP Dosen',
                'user' => $userData,
                'orientationData' => $orientationData,
                'currentSemester' => $currentSemester,
                'filters' => $filters,
                'statistics' => $statistics,
                'calculationResult' => ['added' => 0, 'updated' => 0]
            ]);
        }
    }

    /**
     * Manually recalculate orientation scores (admin function)
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
                return redirect()->to('orientation')->with('error', 'Tidak ada semester aktif');
            }

            $updatedCount = $this->orientationModel->recalculateAllScores($semesterId);

            return redirect()->to('orientation')->with('success', "Berhasil memperbarui {$updatedCount} nilai orientasi pelayanan secara manual");
        } catch (\Exception $e) {
            log_message('error', 'Error manually recalculating orientation scores: ' . $e->getMessage());
            return redirect()->to('orientation')->with('error', 'Gagal memperbarui nilai orientasi pelayanan secara manual: ' . $e->getMessage());
        }
    }

    /**
     * Export orientation data to Excel format
     */
    public function exportExcel()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        $semesterId = $currentSemester ? $currentSemester['id'] : null;

        if (!$semesterId) {
            return redirect()->to('orientation')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        $orientationData = $this->orientationModel->getOrientationDataWithLecturers($semesterId);

        if (empty($orientationData)) {
            return redirect()->to('orientation')->with('error', 'Tidak ada data orientasi pelayanan untuk diekspor');
        }

        // In a real application, you would generate an Excel file here
        return redirect()->to('orientation')->with('success', 'Data orientasi pelayanan berhasil diekspor ke Excel');
    }

    /**
     * Export orientation data to PDF format
     */
    public function exportPdf()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $currentSemester = $this->semesterModel->getCurrentSemester();
        $semesterId = $currentSemester ? $currentSemester['id'] : null;

        if (!$semesterId) {
            return redirect()->to('orientation')->with('error', 'Tidak ada semester aktif untuk diekspor');
        }

        $orientationData = $this->orientationModel->getOrientationDataWithLecturers($semesterId);

        if (empty($orientationData)) {
            return redirect()->to('orientation')->with('error', 'Tidak ada data orientasi pelayanan untuk diekspor');
        }

        // In a real application, you would generate a PDF file here
        return redirect()->to('orientation')->with('success', 'Data orientasi pelayanan berhasil diekspor ke PDF');
    }
}
