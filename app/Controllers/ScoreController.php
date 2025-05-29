<?php

namespace App\Controllers;

use App\Models\ScoreModel;

class ScoreController extends BaseController
{
    protected $scoreModel;

    public function __construct()
    {
        $this->scoreModel = new ScoreModel();
    }

    /**
     * Display the score management interface
     */
    public function index()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user has admin role (uncomment when role system is implemented)
        // if (session()->get('user_role') !== 'admin') {
        //     return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        // }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        // Initialize default ranges if none exist
        if (!$this->scoreModel->hasDefaultRanges()) {
            $this->scoreModel->initializeDefaultRanges();
        }

        // Get score ranges from database
        $scoreRanges = $this->scoreModel->getAllScoreRanges();

        return view('score/index', [
            'pageTitle' => 'Setting Nilai | SKP Dosen',
            'user' => $userData,
            'scoreRanges' => $scoreRanges
        ]);
    }

    /**
     * Update score range data
     */
    public function updateRanges()
    {
        // Ensure user is logged in and authorized
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get POST data
        $category = $this->request->getPost('category');
        $subcategory = $this->request->getPost('subcategory');
        $ranges = $this->request->getPost('ranges');

        if (!$category || !$subcategory || !$ranges) {
            return redirect()->to('score')->with('error', 'Data tidak lengkap');
        }

        try {
            $updatedCount = 0;

            // Update each range
            foreach ($ranges as $rangeId => $rangeData) {
                // Get existing range to validate
                $existingRange = $this->scoreModel->find($rangeId);
                if (!$existingRange) {
                    continue; // Skip if range doesn't exist
                }

                // Prepare update data
                $updateData = [];

                // Always update score (force integer)
                if (isset($rangeData['score'])) {
                    $updateData['score'] = (int)$rangeData['score'];
                }

                // Handle range values based on type and category
                $useIntegers = in_array($existingRange['category'], ['integrity', 'discipline']);

                // Update range_start if provided
                if (isset($rangeData['start']) && $rangeData['start'] !== '') {
                    $updateData['range_start'] = $useIntegers ? (int)$rangeData['start'] : (float)$rangeData['start'];
                } elseif (isset($rangeData['start']) && $rangeData['start'] === '') {
                    $updateData['range_start'] = null;
                }

                // Update range_end if provided
                if (isset($rangeData['end']) && $rangeData['end'] !== '') {
                    $updateData['range_end'] = $useIntegers ? (int)$rangeData['end'] : (float)$rangeData['end'];
                } elseif (isset($rangeData['end']) && $rangeData['end'] === '') {
                    $updateData['range_end'] = null;
                }

                // Update range_label if provided
                if (isset($rangeData['label'])) {
                    $updateData['range_label'] = trim($rangeData['label']);
                }

                // Update range_type if provided
                if (isset($rangeData['type'])) {
                    $updateData['range_type'] = $rangeData['type'];
                }

                // Only update if there are changes
                if (!empty($updateData)) {
                    $this->scoreModel->updateRange($rangeId, $updateData);
                    $updatedCount++;
                }
            }

            if ($updatedCount > 0) {
                return redirect()->to('score')->with('success', "Berhasil memperbarui {$updatedCount} rentang nilai");
            } else {
                return redirect()->to('score')->with('info', 'Tidak ada perubahan yang disimpan');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating score ranges: ' . $e->getMessage());
            return redirect()->to('score')->with('error', 'Gagal memperbarui rentang nilai: ' . $e->getMessage());
        }
    }

    /**
     * Add a new score range
     */
    public function addRange()
    {
        // Ensure user is logged in and authorized
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Validation rules
        $rules = [
            'category' => 'required',
            'subcategory' => 'required',
            'range_type' => 'required|in_list[range,above,below,exact,fixed,boolean]',
            'score' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('score')->with('error', 'Data tidak valid');
        }

        try {
            $category = $this->request->getPost('category');
            $useIntegers = in_array($category, ['integrity', 'discipline']);

            $data = [
                'category' => $category,
                'subcategory' => $this->request->getPost('subcategory'),
                'range_type' => $this->request->getPost('range_type'),
                'range_start' => $this->request->getPost('range_start') ?
                    ($useIntegers ? (int)$this->request->getPost('range_start') : (float)$this->request->getPost('range_start')) : null,
                'range_end' => $this->request->getPost('range_end') ?
                    ($useIntegers ? (int)$this->request->getPost('range_end') : (float)$this->request->getPost('range_end')) : null,
                'range_label' => $this->request->getPost('range_label'),
                'score' => (int)$this->request->getPost('score'),
                'editable' => true
            ];

            $this->scoreModel->addRange($data);

            return redirect()->to('score')->with('success', 'Rentang nilai baru berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error adding score range: ' . $e->getMessage());
            return redirect()->to('score')->with('error', 'Gagal menambahkan rentang nilai: ' . $e->getMessage());
        }
    }

    /**
     * Get score for specific input (AJAX)
     */
    public function getScoreForInput()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('score');
        }

        $category = $this->request->getPost('category');
        $subcategory = $this->request->getPost('subcategory');
        $value = $this->request->getPost('value');

        if (!$category || !$subcategory || $value === null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }

        try {
            $score = (int)$this->scoreModel->calculateScore($category, $subcategory, $value);
            $ranges = $this->scoreModel->getRangesBySubcategory($category, $subcategory);

            return $this->response->setJSON([
                'status' => 'success',
                'score' => $score,
                'ranges' => $ranges,
                'message' => "Nilai {$value} mendapat skor {$score} poin"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting score for input: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghitung skor'
            ]);
        }
    }

    /**
     * Delete a score range
     */
    public function deleteRange()
    {
        // Ensure user is logged in and authorized
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $rangeId = $this->request->getPost('range_id');

        if (!$rangeId) {
            return redirect()->to('score')->with('error', 'ID rentang nilai tidak valid');
        }

        try {
            // Check if range exists
            $range = $this->scoreModel->find($rangeId);
            if (!$range) {
                return redirect()->to('score')->with('error', 'Rentang nilai tidak ditemukan');
            }

            // // Check if range is editable
            // if (!$range->editable) {
            //     return redirect()->to('score')->with('error', 'Rentang nilai ini tidak dapat dihapus');
            // }

            $this->scoreModel->deleteRange($rangeId);

            return redirect()->to('score')->with('success', 'Rentang nilai berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting score range: ' . $e->getMessage());
            return redirect()->to('score')->with('error', 'Gagal menghapus rentang nilai');
        }
    }

    /**
     * Calculate score for given values
     */
    public function calculateScore()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('score');
        }

        $category = $this->request->getPost('category');
        $subcategory = $this->request->getPost('subcategory');
        $value = $this->request->getPost('value');

        if (!$category || !$subcategory || $value === null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap'
            ]);
        }

        try {
            $score = (int)$this->scoreModel->calculateScore($category, $subcategory, $value);

            return $this->response->setJSON([
                'status' => 'success',
                'score' => $score,
                'message' => 'Skor berhasil dihitung'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error calculating score: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghitung skor'
            ]);
        }
    }

    /**
     * Reset score ranges to default
     */
    public function resetToDefault()
    {
        // Ensure user is logged in and authorized
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Additional security check for admin role
        // if (session()->get('user_role') !== 'admin') {
        //     return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        // }

        try {
            // Delete all existing ranges
            $this->scoreModel->where('editable', true)->delete();

            // Initialize default ranges
            $this->scoreModel->initializeDefaultRanges();

            return redirect()->to('score')->with('success', 'Rentang nilai berhasil direset ke default');
        } catch (\Exception $e) {
            log_message('error', 'Error resetting score ranges: ' . $e->getMessage());
            return redirect()->to('score')->with('error', 'Gagal mereset rentang nilai');
        }
    }

    /**
     * Export score ranges configuration
     */
    public function exportConfig()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $ranges = $this->scoreModel->findAll();

            $filename = 'score_ranges_config_' . date('Y-m-d_H-i-s') . '.json';

            return $this->response
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody(json_encode($ranges, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            log_message('error', 'Error exporting score config: ' . $e->getMessage());
            return redirect()->to('score')->with('error', 'Gagal mengekspor konfigurasi');
        }
    }
}
