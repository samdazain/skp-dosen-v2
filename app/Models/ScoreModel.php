<?php

namespace App\Models;

use CodeIgniter\Model;

class ScoreModel extends Model
{
    protected $table = 'score_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category',
        'subcategory',
        'range_type',
        'range_start',
        'range_end',
        'range_label',
        'score',
        'editable',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;

    /**
     * Get all score ranges organized by category and subcategory
     */
    public function getAllScoreRanges()
    {
        $ranges = $this->orderBy('category')
            ->orderBy('subcategory')
            ->orderBy('score', 'DESC')
            ->findAll();

        return $this->organizeRangesByCategory($ranges);
    }

    /**
     * Get score ranges for a specific category
     */
    public function getRangesByCategory($category)
    {
        return $this->where('category', $category)
            ->orderBy('subcategory')
            ->orderBy('score', 'DESC')
            ->findAll();
    }

    /**
     * Get score ranges for a specific subcategory
     */
    public function getRangesBySubcategory($category, $subcategory)
    {
        return $this->where('category', $category)
            ->where('subcategory', $subcategory)
            ->orderBy('score', 'DESC')
            ->findAll();
    }

    /**
     * Calculate score based on value and category/subcategory
     */
    public function calculateScore($category, $subcategory, $value)
    {
        // Handle commitment category specifically
        if ($category === 'commitment') {
            return $this->calculateCommitmentScore($subcategory, $value);
        }

        $ranges = $this->getRangesBySubcategory($category, $subcategory);

        foreach ($ranges as $range) {
            if ($this->valueInRange($value, $range)) {
                return (int)$range['score'];
            }
        }

        return 0; // Default score if no range matches
    }

    /**
     * Calculate commitment-specific scores
     */
    private function calculateCommitmentScore($subcategory, $value)
    {
        switch ($subcategory) {
            case 'competency':
                // Handle competency scoring
                if ($value === 'active' || $value === true || $value === 1 || $value === '1') {
                    return 88; // Active competency score
                } else {
                    return 70; // Inactive competency score
                }

            case 'tri_dharma':
                // Handle tri dharma scoring
                if ($value === 'pass' || $value === true || $value === 1 || $value === '1') {
                    return 88; // Pass tri dharma score
                } else {
                    return 70; // Fail tri dharma score
                }

            default:
                return 0;
        }
    }

    /**
     * Check if a value falls within a specific range
     */
    private function valueInRange($value, $range)
    {
        // Convert value to appropriate type based on category
        if (in_array($range['category'], ['integrity', 'discipline'])) {
            $value = (int)$value; // Force integer for integrity and discipline
        } else {
            $value = is_numeric($value) ? (float)$value : $value;
        }

        switch ($range['range_type']) {
            case 'range':
                return $value >= $range['range_start'] && $value <= $range['range_end'];
            case 'above':
                return $value > $range['range_start'];
            case 'below':
                return $value < $range['range_start'];
            case 'exact':
                return $value == $range['range_start'];
            case 'boolean':
                $boolValue = ($value === true || $value === 'true' || $value === 1 || $value === '1');
                $rangeValue = ($range['range_label'] === 'true' || $range['range_label'] === '1');
                return $boolValue === $rangeValue;
            case 'fixed':
                return $value === $range['range_label'];
            default:
                return false;
        }
    }

    /**
     * Add a new score range
     */
    public function addRange($data)
    {
        $insertData = [
            'category' => $data['category'],
            'subcategory' => $data['subcategory'],
            'range_type' => $data['range_type'],
            'range_start' => $data['range_start'] ?? null,
            'range_end' => $data['range_end'] ?? null,
            'range_label' => $data['range_label'] ?? null,
            'score' => (int)$data['score'],
            'editable' => $data['editable'] ?? true
        ];

        return $this->insert($insertData);
    }

    /**
     * Update score range
     */
    public function updateRange($id, $data)
    {
        // Ensure score is always integer
        if (isset($data['score'])) {
            $data['score'] = (int)$data['score'];
        }

        return $this->update($id, $data);
    }

    /**
     * Delete score range
     */
    public function deleteRange($id)
    {
        return $this->delete($id);
    }

    /**
     * Initialize default score ranges based on the provided conventions
     */
    public function initializeDefaultRanges()
    {
        $defaultRanges = [
            // Commitment ranges
            [
                'category' => 'commitment',
                'subcategory' => 'competency',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'active',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'commitment',
                'subcategory' => 'competency',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'inactive',
                'score' => 70,
                'editable' => true
            ],
            [
                'category' => 'commitment',
                'subcategory' => 'tri_dharma',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'pass',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'commitment',
                'subcategory' => 'tri_dharma',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'fail',
                'score' => 70,
                'editable' => true
            ]
        ];

        foreach ($defaultRanges as $range) {
            // Check if range already exists
            $existing = $this->where('category', $range['category'])
                ->where('subcategory', $range['subcategory'])
                ->where('range_label', $range['range_label'])
                ->first();

            if (!$existing) {
                $this->insert($range);
            }
        }

        return true;
    }

    /**
     * Get score for a specific value dynamically from database
     */
    public function getScoreForValue($category, $subcategory, $value)
    {
        return $this->calculateScore($category, $subcategory, $value);
    }

    /**
     * Get all scores for a category/subcategory as options
     */
    public function getScoreOptions($category, $subcategory)
    {
        $ranges = $this->getRangesBySubcategory($category, $subcategory);
        $options = [];

        foreach ($ranges as $range) {
            $label = $range['range_label'] ?: ($range['range_type'] === 'range' ?
                    "{$range['range_start']}-{$range['range_end']}" :
                    $range['range_start']);

            $options[$range['id']] = [
                'label' => $label,
                'score' => $range['score']
            ];
        }

        return $options;
    }

    /**
     * Organize ranges by category for view display
     */
    private function organizeRangesByCategory($ranges)
    {
        $organized = [
            'integrity' => [],
            'discipline' => [],
            'commitment' => [],
            'cooperation' => [],
            'service_orientation' => []
        ];

        foreach ($ranges as $range) {
            if (isset($organized[$range['category']])) {
                if (!isset($organized[$range['category']][$range['subcategory']])) {
                    $organized[$range['category']][$range['subcategory']] = [];
                }
                $organized[$range['category']][$range['subcategory']][] = $range;
            }
        }

        return $organized;
    }

    /**
     * Check if default ranges exist
     */
    public function hasDefaultRanges()
    {
        return $this->countAll() > 0;
    }
}
