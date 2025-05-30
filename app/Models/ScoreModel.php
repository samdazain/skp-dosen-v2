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
            ->orderBy('range_start', 'ASC')
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
            ->orderBy('range_start', 'ASC')
            ->findAll();
    }

    /**
     * Get score ranges for a specific subcategory
     */
    public function getRangesBySubcategory($category, $subcategory)
    {
        return $this->where('category', $category)
            ->where('subcategory', $subcategory)
            ->orderBy('range_start', 'ASC')
            ->findAll();
    }

    /**
     * Calculate score based on value and category/subcategory
     */
    public function calculateScore($category, $subcategory, $value)
    {
        $ranges = $this->getRangesBySubcategory($category, $subcategory);

        foreach ($ranges as $range) {
            if ($this->valueInRange($value, $range)) {
                return (int)$range['score']; // Force integer return
            }
        }

        return 0; // Default score if no range matches
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
            $value = (float)$value; // Allow decimal for other categories
        }

        switch ($range['range_type']) {
            case 'range':
                if (in_array($range['category'], ['integrity', 'discipline'])) {
                    // Integer comparison for integrity and discipline
                    return $value >= (int)$range['range_start'] && $value <= (int)$range['range_end'];
                } else {
                    // Float comparison for other categories
                    return $value >= (float)$range['range_start'] && $value <= (float)$range['range_end'];
                }

            case 'above':
                if (in_array($range['category'], ['integrity', 'discipline'])) {
                    return $value > (int)$range['range_start'];
                } else {
                    return $value > (float)$range['range_start'];
                }

            case 'below':
                if (in_array($range['category'], ['integrity', 'discipline'])) {
                    return $value < (int)$range['range_end'];
                } else {
                    return $value < (float)$range['range_end'];
                }

            case 'exact':
                if (in_array($range['category'], ['integrity', 'discipline'])) {
                    return $value == (int)$range['range_start'];
                } else {
                    return $value == (float)$range['range_start'];
                }

            case 'boolean':
                // Handle boolean logic based on label
                $positiveLabels = ['ada', 'lulus', 'aktif', 'yes', 'true', '1'];
                $rangeIsPositive = in_array(strtolower($range['range_label']), $positiveLabels);
                $valueIsPositive = in_array(strtolower($value), $positiveLabels) || $value === true || $value === 1;

                return $rangeIsPositive === $valueIsPositive;

            case 'fixed':
                return strtolower($range['range_label']) === strtolower($value);

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
            'score' => (int)$data['score'], // Force integer conversion
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
            // Data Integritas - INTEGER VALUES
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 0, 'range_end' => 4, 'score' => 60, 'range_label' => '0-4'],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 5, 'range_end' => 7, 'score' => 75, 'range_label' => '5-7'],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'range', 'range_start' => 8, 'range_end' => 10, 'score' => 85, 'range_label' => '8-10'],
            ['category' => 'integrity', 'subcategory' => 'teaching_attendance', 'range_type' => 'above', 'range_start' => 10, 'range_end' => null, 'score' => 88, 'range_label' => '>10'],

            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'score' => 75, 'range_label' => '1-2'],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'score' => 80, 'range_label' => '3-4'],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'range', 'range_start' => 5, 'range_end' => 6, 'score' => 85, 'range_label' => '5-6'],
            ['category' => 'integrity', 'subcategory' => 'courses_taught', 'range_type' => 'above', 'range_start' => 6, 'range_end' => null, 'score' => 88, 'range_label' => '>6'],

            // Data Disiplin - INTEGER VALUES (corrected subcategory names)
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'score' => 85, 'range_label' => '1-2'],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'score' => 80, 'range_label' => '3-4'],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'range', 'range_start' => 5, 'range_end' => 6, 'score' => 75, 'range_label' => '5-6'],
            ['category' => 'discipline', 'subcategory' => 'daily_attendance', 'range_type' => 'above', 'range_start' => 6, 'range_end' => null, 'score' => 60, 'range_label' => '>6'],

            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => 0, 'score' => 88, 'range_label' => '0'],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'score' => 85, 'range_label' => '1-2'],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'score' => 80, 'range_label' => '3-4'],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 5, 'range_end' => 6, 'score' => 75, 'range_label' => '5-6'],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'range', 'range_start' => 7, 'range_end' => 8, 'score' => 70, 'range_label' => '7-8'],
            ['category' => 'discipline', 'subcategory' => 'morning_exercise', 'range_type' => 'above', 'range_start' => 8, 'range_end' => null, 'score' => 60, 'range_label' => '>8 atau 3 kali berturut alpha'],

            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'exact', 'range_start' => 0, 'range_end' => 0, 'score' => 88, 'range_label' => '0'],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'range', 'range_start' => 1, 'range_end' => 2, 'score' => 80, 'range_label' => '1-2'],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'range', 'range_start' => 3, 'range_end' => 4, 'score' => 70, 'range_label' => '3-4'],
            ['category' => 'discipline', 'subcategory' => 'ceremony_attendance', 'range_type' => 'above', 'range_start' => 4, 'range_end' => null, 'score' => 60, 'range_label' => '>4'],

            // Komitmen - BOOLEAN VALUES
            ['category' => 'commitment', 'subcategory' => 'competency', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'score' => 88, 'range_label' => 'Ada'],
            ['category' => 'commitment', 'subcategory' => 'competency', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'score' => 70, 'range_label' => 'Tidak'],

            ['category' => 'commitment', 'subcategory' => 'tri_dharma', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'score' => 88, 'range_label' => 'Lulus'],
            ['category' => 'commitment', 'subcategory' => 'tri_dharma', 'range_type' => 'boolean', 'range_start' => null, 'range_end' => null, 'score' => 70, 'range_label' => 'Tidak Lulus'],

            // Kerjasama - FIXED VALUES
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'score' => 60, 'range_label' => 'Tidak Kooperatif'],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'score' => 75, 'range_label' => 'Cukup Kooperatif'],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'score' => 80, 'range_label' => 'Kooperatif'],
            ['category' => 'cooperation', 'subcategory' => 'cooperation_level', 'range_type' => 'fixed', 'range_start' => null, 'range_end' => null, 'score' => 88, 'range_label' => 'Sangat Kooperatif'],

            // Orientasi Pelayanan - DECIMAL VALUES
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'above', 'range_start' => 3.5, 'range_end' => null, 'score' => 88, 'range_label' => '>3.5'],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 3.0, 'range_end' => 3.5, 'score' => 85, 'range_label' => '3.0 - 3.5'],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 2.75, 'range_end' => 3.0, 'score' => 80, 'range_label' => '2.75 - 3.0'],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'range', 'range_start' => 2.5, 'range_end' => 2.75, 'score' => 70, 'range_label' => '2.5 - 2.75'],
            ['category' => 'orientation', 'subcategory' => 'teaching_questionnaire', 'range_type' => 'below', 'range_start' => null, 'range_end' => 2.5, 'score' => 60, 'range_label' => '<2.5'],
        ];

        foreach ($defaultRanges as $range) {
            $range['editable'] = true;
            $this->insert($range);
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
            $options[] = [
                'range_id' => $range['id'],
                'label' => $range['range_label'],
                'score' => $range['score'],
                'range_type' => $range['range_type']
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
            'integrity' => [
                'title' => 'Data Integritas',
                'subcategories' => [
                    'teaching_attendance' => [
                        'title' => 'Kehadiran Mengajar',
                        'ranges' => []
                    ],
                    'courses_taught' => [
                        'title' => 'Jumlah MK di Ampu',
                        'ranges' => []
                    ]
                ]
            ],
            'discipline' => [
                'title' => 'Data Disiplin',
                'subcategories' => [
                    'daily_attendance' => [
                        'title' => 'Presensi Harian (jumlah alpha)',
                        'ranges' => []
                    ],
                    'morning_exercise' => [
                        'title' => 'Presensi Senam Pagi (jumlah alpha)',
                        'ranges' => []
                    ],
                    'ceremony_attendance' => [
                        'title' => 'Presensi Upacara (jumlah alpha)',
                        'ranges' => []
                    ]
                ]
            ],
            'commitment' => [
                'title' => 'Data Komitmen',
                'subcategories' => [
                    'competency' => [
                        'title' => 'Kompetensi (aktif)',
                        'ranges' => []
                    ],
                    'tri_dharma' => [
                        'title' => 'Tri Dharma (BKD)',
                        'ranges' => []
                    ]
                ]
            ],
            'cooperation' => [
                'title' => 'Kerjasama (Koprodi / Dekanat)',
                'subcategories' => [
                    'cooperation_level' => [
                        'title' => 'Tingkat Kerjasama',
                        'ranges' => []
                    ]
                ]
            ],
            'orientation' => [
                'title' => 'Orientasi Pelayanan',
                'subcategories' => [
                    'teaching_questionnaire' => [
                        'title' => 'Kuisioner Mengajar',
                        'ranges' => []
                    ]
                ]
            ]
        ];

        // Populate ranges into organized structure
        foreach ($ranges as $range) {
            $category = $range['category'];
            $subcategory = $range['subcategory'];

            if (isset($organized[$category]['subcategories'][$subcategory])) {
                // Determine boolean value for boolean types
                $value = null;
                if ($range['range_type'] === 'boolean' && isset($range['range_label'])) {
                    $value = in_array(strtolower($range['range_label']), ['ada', 'lulus', 'aktif', 'yes', 'true', '1']);
                }

                $organized[$category]['subcategories'][$subcategory]['ranges'][] = [
                    'id' => $range['id'],
                    'start' => (int)$range['range_start'],
                    'end' => (int)$range['range_end'],
                    'score' => (int)$range['score'], // Force integer display
                    'label' => $range['range_label'] ?? '',
                    'type' => $range['range_type'],
                    'value' => $value,
                    'editable' => $range['editable'] ?? true
                ];
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
