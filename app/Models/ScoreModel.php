<?php

namespace App\Models;

use CodeIgniter\Model;

class ScoreModel extends Model
{
    protected $table = 'score_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category',
        'subcategory',
        'range_type',
        'range_start',
        'range_end',
        'range_label',
        'score',
        'editable'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all score ranges organized by category
     */
    public function getAllScoreRanges()
    {
        try {
            $ranges = $this->orderBy('category', 'ASC')
                ->orderBy('subcategory', 'ASC')
                ->orderBy('score', 'DESC')
                ->findAll();

            // Ensure we always return an array, even if empty
            return is_array($ranges) ? $ranges : [];
        } catch (\Exception $e) {
            log_message('error', 'Error getting score ranges: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get ranges by specific category
     */
    public function getRangesByCategory($category)
    {
        try {
            return $this->where('category', $category)
                ->orderBy('subcategory', 'ASC')
                ->orderBy('score', 'DESC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', "Error getting ranges for category {$category}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get ranges by category and subcategory
     */
    public function getRangesBySubcategory($category, $subcategory)
    {
        try {
            return $this->where('category', $category)
                ->where('subcategory', $subcategory)
                ->orderBy('score', 'DESC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', "Error getting ranges for {$category}/{$subcategory}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate score for given category, subcategory and value
     */
    public function calculateScore($category, $subcategory, $value)
    {
        try {
            return $this->getScoreForValue($category, $subcategory, $value);
        } catch (\Exception $e) {
            log_message('error', "Error calculating score for {$category}/{$subcategory}/{$value}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get score for a specific value in a category/subcategory
     * Enhanced to handle different range types properly
     */
    public function getScoreForValue($category, $subcategory, $value)
    {
        try {
            // Get all ranges for this category/subcategory
            $ranges = $this->where('category', $category)
                ->where('subcategory', $subcategory)
                ->orderBy('score', 'DESC') // Order by score descending for consistent results
                ->findAll();

            if (empty($ranges)) {
                log_message('warning', "No score ranges found for {$category}/{$subcategory}");
                throw new \Exception("No score ranges found for {$category}/{$subcategory}");
            }

            // Log the search attempt
            log_message('debug', "Searching score for {$category}/{$subcategory} with value: '{$value}'");
            log_message('debug', "Available ranges: " . json_encode($ranges));

            foreach ($ranges as $range) {
                $match = false;

                switch ($range['range_type']) {
                    case 'boolean':
                    case 'fixed':
                        // For boolean and fixed types, match against range_label
                        $match = $this->matchLabelValue($value, $range['range_label']);
                        log_message('debug', "Checking {$range['range_type']} match: '{$value}' vs '{$range['range_label']}' = " . ($match ? 'true' : 'false'));
                        break;

                    case 'exact':
                        // For exact matches, compare with range_start
                        $match = ($value == $range['range_start']);
                        break;

                    case 'range':
                        // For ranges, check if value falls within range_start and range_end
                        $numericValue = is_numeric($value) ? (float)$value : $value;
                        $match = ($numericValue >= $range['range_start'] && $numericValue <= $range['range_end']);
                        break;

                    case 'above':
                        // For above type, check if value is greater than range_start
                        $numericValue = is_numeric($value) ? (float)$value : $value;
                        $match = ($numericValue > $range['range_start']);
                        break;

                    case 'below':
                        // For below type, check if value is less than range_start
                        $numericValue = is_numeric($value) ? (float)$value : $value;
                        $match = ($numericValue < $range['range_start']);
                        break;
                }

                if ($match) {
                    log_message('debug', "Found matching range for {$category}/{$subcategory}/{$value}: score = {$range['score']}");
                    return (int)$range['score'];
                }
            }

            // If no match found, throw exception
            throw new \Exception("No matching score range found for {$category}/{$subcategory} with value: {$value}");
        } catch (\Exception $e) {
            log_message('error', "Error getting score for {$category}/{$subcategory}/{$value}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Match label values with special handling for different formats
     * Handles mappings between database values and display labels
     */
    private function matchLabelValue($inputValue, $rangeLabel)
    {
        // Normalize both values for comparison
        $normalizedInput = strtolower(trim((string)$inputValue));
        $normalizedLabel = strtolower(trim((string)$rangeLabel));

        // Direct match first
        if ($normalizedInput === $normalizedLabel) {
            return true;
        }

        // Special mappings for commitment category
        $commitmentMappings = [
            'active' => ['ada', 'aktif', 'active'],
            'inactive' => ['tidak', 'tidak ada', 'inactive'],
            'pass' => ['lulus', 'pass'],
            'fail' => ['tidak lulus', 'fail', 'gagal'],
            '1' => ['lulus', 'pass', 'ada', 'aktif'],
            '0' => ['tidak lulus', 'fail', 'tidak', 'tidak ada'],
            'true' => ['lulus', 'pass', 'ada', 'aktif'],
            'false' => ['tidak lulus', 'fail', 'tidak', 'tidak ada']
        ];

        // Special mappings for cooperation category
        $cooperationMappings = [
            'not_cooperative' => ['tidak kooperatif', 'not cooperative'],
            'fair' => ['cukup kooperatif', 'fair'],
            'cooperative' => ['kooperatif', 'cooperative'],
            'very_cooperative' => ['sangat kooperatif', 'very cooperative']
        ];

        // Check commitment mappings
        if (isset($commitmentMappings[$normalizedInput])) {
            foreach ($commitmentMappings[$normalizedInput] as $mapping) {
                if ($normalizedLabel === $mapping) {
                    return true;
                }
            }
        }

        // Check cooperation mappings
        if (isset($cooperationMappings[$normalizedInput])) {
            foreach ($cooperationMappings[$normalizedInput] as $mapping) {
                if ($normalizedLabel === $mapping) {
                    return true;
                }
            }
        }

        // Reverse check - if range label maps to input value
        foreach ($commitmentMappings as $key => $mappings) {
            if (in_array($normalizedLabel, $mappings) && $normalizedInput === $key) {
                return true;
            }
        }

        foreach ($cooperationMappings as $key => $mappings) {
            if (in_array($normalizedLabel, $mappings) && $normalizedInput === $key) {
                return true;
            }
        }

        // Boolean value conversions
        if (
            in_array($normalizedInput, ['1', 'true', 'yes', 'ya']) &&
            in_array($normalizedLabel, ['ada', 'aktif', 'lulus', 'pass'])
        ) {
            return true;
        }

        if (
            in_array($normalizedInput, ['0', 'false', 'no', 'tidak']) &&
            in_array($normalizedLabel, ['tidak', 'tidak ada', 'tidak lulus', 'fail'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Add a new score range
     */
    public function addRange($data)
    {
        try {
            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Error adding score range: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update score range
     */
    public function updateRange($id, $data)
    {
        try {
            return $this->update($id, $data);
        } catch (\Exception $e) {
            log_message('error', "Error updating score range {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete score range
     */
    public function deleteRange($id)
    {
        try {
            return $this->delete($id);
        } catch (\Exception $e) {
            log_message('error', "Error deleting score range {$id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initialize default score ranges if none exist
     */
    public function initializeDefaultRanges()
    {
        if ($this->hasDefaultRanges()) {
            return false; // Already initialized
        }

        try {
            $this->db->transStart();

            // Initialize default ranges for each category
            $this->initializeIntegrityRanges();
            $this->initializeDisciplineRanges();
            $this->initializeCommitmentRanges();
            $this->initializeCooperationRanges();
            $this->initializeOrientationRanges();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed during initialization');
            }

            log_message('info', 'Successfully initialized default score ranges');
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error initializing default ranges: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if default ranges already exist
     */
    public function hasDefaultRanges()
    {
        $count = $this->countAll();
        return $count > 0;
    }

    /**
     * Initialize integrity score ranges
     */
    private function initializeIntegrityRanges()
    {
        $integrityRanges = [
            // Teaching attendance subcategory
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 0, 'range_end' => 50, 'range_label' => null, 'score' => 60, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 51, 'range_end' => 75, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 76, 'range_end' => 90, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 91, 'range_end' => 100, 'range_label' => null, 'score' => 88, 'editable' => true],

            // Courses taught subcategory
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => null, 'range_label' => null, 'score' => 60, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'exact', 'range_start' => 1, 'range_end' => null, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'exact', 'range_start' => 2, 'range_end' => null, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'above', 'range_start' => 2, 'range_end' => null, 'range_label' => null, 'score' => 88, 'editable' => true]
        ];

        $this->insertBatch($integrityRanges);
    }

    /**
     * Initialize discipline score ranges
     */
    private function initializeDisciplineRanges()
    {
        $disciplineRanges = [
            // Daily attendance
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => null, 'range_label' => null, 'score' => 88, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'above', 'range_start' => 4, 'range_end' => null, 'range_label' => null, 'score' => 60, 'editable' => true],

            // Morning exercise
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => null, 'range_label' => null, 'score' => 88, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'above', 'range_start' => 4, 'range_end' => null, 'range_label' => null, 'score' => 60, 'editable' => true],

            // Ceremony attendance
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => null, 'range_label' => null, 'score' => 88, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'above', 'range_start' => 4, 'range_end' => null, 'range_label' => null, 'score' => 60, 'editable' => true]
        ];

        $this->insertBatch($disciplineRanges);
    }

    /**
     * Initialize commitment score ranges
     */
    private function initializeCommitmentRanges()
    {
        $commitmentRanges = [
            // Competency subcategory
            ['category' => 'commitment', 'subcategory' => 'competency', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'range_label' => 'Ada', 'score' => 88, 'editable' => true],
            ['category' => 'commitment', 'subcategory' => 'competency', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'range_label' => 'Tidak', 'score' => 70, 'editable' => true],

            // Tri Dharma subcategory
            ['category' => 'commitment', 'subcategory' => 'tri_dharma', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'range_label' => 'Lulus', 'score' => 88, 'editable' => true],
            ['category' => 'commitment', 'subcategory' => 'tri_dharma', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'range_label' => 'Tidak Lulus', 'score' => 70, 'editable' => true]
        ];

        $this->insertBatch($commitmentRanges);
    }

    /**
     * Initialize cooperation score ranges
     */
    private function initializeCooperationRanges()
    {
        $cooperationRanges = [
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'range_label' => 'Tidak Kooperatif', 'score' => 60, 'editable' => true],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'range_label' => 'Cukup Kooperatif', 'score' => 75, 'editable' => true],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'range_label' => 'Kooperatif', 'score' => 80, 'editable' => true],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'range_label' => 'Sangat Kooperatif', 'score' => 88, 'editable' => true]
        ];

        $this->insertBatch($cooperationRanges);
    }

    /**
     * Initialize orientation score ranges
     */
    private function initializeOrientationRanges()
    {
        $orientationRanges = [
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 1.0, 'range_end' => 2.0, 'range_label' => null, 'score' => 60, 'editable' => true],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 2.1, 'range_end' => 3.0, 'range_label' => null, 'score' => 70, 'editable' => true],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 3.1, 'range_end' => 4.0, 'range_label' => null, 'score' => 80, 'editable' => true],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 4.1, 'range_end' => 5.0, 'range_label' => null, 'score' => 88, 'editable' => true]
        ];

        $this->insertBatch($orientationRanges);
    }

    /**
     * Get score options for a specific category/subcategory (for form display)
     */
    public function getScoreOptions($category, $subcategory)
    {
        try {
            $ranges = $this->getRangesBySubcategory($category, $subcategory);
            $options = [];

            foreach ($ranges as $range) {
                if ($range['range_type'] === 'boolean' || $range['range_type'] === 'fixed') {
                    $options[] = [
                        'value' => $range['range_label'],
                        'label' => $range['range_label'],
                        'score' => $range['score']
                    ];
                }
            }

            return $options;
        } catch (\Exception $e) {
            log_message('error', "Error getting score options for {$category}/{$subcategory}: " . $e->getMessage());
            return [];
        }
    }
}
