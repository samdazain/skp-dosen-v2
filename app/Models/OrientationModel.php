<?php

namespace App\Models;

use CodeIgniter\Model;

class OrientationModel extends Model
{
    protected $table = 'service_orientation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'questionnaire_score',
        'score',
        'updated_by',
        'updated_at'
    ];
    protected $useTimestamps = false; // We'll handle updated_at manually

    protected $lecturerModel;
    protected $scoreModel;

    /**
     * Position hierarchy for sorting
     */
    protected $positions = [
        'DEKAN',
        'WAKIL DEKAN I',
        'WAKIL DEKAN II',
        'WAKIL DEKAN III',
        'KOORPRODI IF',
        'KOORPRODI SI',
        'KOORPRODI SD',
        'KOORPRODI BD',
        'KOORPRODI MTI',
        'Ka Lab SCR',
        'Ka Lab PPSTI',
        'Ka Lab SOLUSI',
        'Ka Lab MSI',
        'Ka Lab Sains Data',
        'Ka Lab BISDI',
        'Ka Lab MTI',
        'Ka UPT TIK',
        'Ka UPA PKK',
        'Ka Pengembangan Pembelajaran LPMPP',
        'PPMB',
        'KOORDINATOR PUSAT KARIR DAN TRACER STUDY',
        'LSP UPNVJT',
        'UPT TIK',
        'Dosen Prodi'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->lecturerModel = new LecturerModel();
        $this->scoreModel = new ScoreModel();
    }

    /**
     * Get orientation data with lecturer information for current semester
     * Auto-populate missing lecturers and include position ordering with filtering
     */
    public function getOrientationDataWithLecturers($semesterId = null, $filters = [])
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [];
        }

        // Auto-populate orientation table with all lecturers for this semester
        $this->autoPopulateOrientationData($semesterId);

        // Auto-recalculate scores that are 0
        $this->recalculateZeroScores($semesterId);

        // Build the query with filters
        $builder = $this->select('
                service_orientation.*,
                lecturers.nip,
                lecturers.name as lecturer_name,
                lecturers.position,
                lecturers.study_program
            ')
            ->join('lecturers', 'lecturers.id = service_orientation.lecturer_id')
            ->where('service_orientation.semester_id', $semesterId);

        // Apply filters
        if (!empty($filters['position'])) {
            $builder->where('lecturers.position', $filters['position']);
        }

        if (!empty($filters['study_program'])) {
            $builder->where('lecturers.study_program', $filters['study_program']);
        }

        if (!empty($filters['score_range'])) {
            switch ($filters['score_range']) {
                case 'excellent':
                    $builder->where('service_orientation.score >=', 88);
                    break;
                case 'good':
                    $builder->where('service_orientation.score >=', 80)
                        ->where('service_orientation.score <', 88);
                    break;
                case 'fair':
                    $builder->where('service_orientation.score >=', 70)
                        ->where('service_orientation.score <', 80);
                    break;
                case 'poor':
                    $builder->where('service_orientation.score <', 70);
                    break;
            }
        }

        $data = $builder->findAll();

        // Sort by position hierarchy, then by name
        usort($data, function ($a, $b) {
            $aPos = array_search($a['position'], $this->positions);
            $bPos = array_search($b['position'], $this->positions);

            // If position not found, put at end
            $aPos = $aPos === false ? count($this->positions) : $aPos;
            $bPos = $bPos === false ? count($this->positions) : $bPos;

            if ($aPos === $bPos) {
                return strcmp($a['lecturer_name'], $b['lecturer_name']);
            }

            return $aPos - $bPos;
        });

        return $data;
    }

    /**
     * Auto-populate orientation table with all lecturers for given semester
     * NO hardcoded values - use database calculation
     */
    public function autoPopulateOrientationData($semesterId)
    {
        try {
            // Get all lecturers
            $lecturers = $this->lecturerModel->findAll();

            if (empty($lecturers)) {
                return 0;
            }

            // Get existing records for this semester to avoid duplicates
            $existingRecords = $this->where('semester_id', $semesterId)
                ->findColumn('lecturer_id');

            $newRecords = [];
            $addedCount = 0;

            foreach ($lecturers as $lecturer) {
                // Check if orientation record exists for this lecturer and semester
                if (!in_array($lecturer['id'], $existingRecords)) {
                    // Get the default questionnaire score from database
                    $defaultQuestionnaireScore = $this->getDefaultQuestionnaireScore();

                    // Calculate score using database settings - NO hardcoding
                    $calculatedScore = $this->calculateOrientationScore($defaultQuestionnaireScore);

                    $newRecords[] = [
                        'lecturer_id' => $lecturer['id'],
                        'semester_id' => $semesterId,
                        'questionnaire_score' => $defaultQuestionnaireScore,
                        'score' => $calculatedScore, // Calculated from database, not hardcoded
                        'updated_by' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $addedCount++;
                }
            }

            // Bulk insert for better performance
            if (!empty($newRecords)) {
                try {
                    $this->insertBatch($newRecords);
                    log_message('info', "Auto-populated {$addedCount} orientation records for semester {$semesterId}");
                } catch (\Exception $e) {
                    log_message('error', 'Error auto-populating orientation data: ' . $e->getMessage());
                    throw $e;
                }
            }

            return $addedCount;
        } catch (\Exception $e) {
            log_message('error', 'Error in autoPopulateOrientationData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get default questionnaire score from database configuration
     * This replaces hardcoded values
     */
    private function getDefaultQuestionnaireScore()
    {
        try {
            // Get the middle range value from database for default
            $ranges = $this->scoreModel->getRangesBySubcategory('orientation', 'teaching_questionnaire');

            if (!empty($ranges)) {
                // Find a middle range or return the first available range
                foreach ($ranges as $range) {
                    if ($range['range_start'] !== null && $range['range_end'] !== null) {
                        return ($range['range_start'] + $range['range_end']) / 2;
                    }
                }

                // If no range found, get the first available range start
                return $ranges[0]['range_start'] ?? 2.5;
            }

            // Absolute fallback - but should never reach here if database is properly configured
            log_message('warning', 'No orientation score ranges found in database, using fallback value');
            return 2.5;
        } catch (\Exception $e) {
            log_message('error', 'Error getting default questionnaire score: ' . $e->getMessage());
            return 2.5; // Minimal fallback
        }
    }

    /**
     * Calculate orientation score based on questionnaire score using database settings
     * No hardcoded values - always uses ScoreModel
     */
    public function calculateOrientationScore($questionnaireScore)
    {
        try {
            // Use ScoreModel to get score from database settings
            $score = $this->scoreModel->calculateScore('orientation', 'teaching_questionnaire', (float)$questionnaireScore);

            return (int)$score;
        } catch (\Exception $e) {
            log_message('error', "Error calculating orientation score for questionnaire score {$questionnaireScore}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update orientation score for a lecturer
     */
    public function updateOrientationScore($lecturerId, $semesterId, $questionnaireScore, $updatedBy = null)
    {
        // Calculate score
        $score = $this->calculateOrientationScore($questionnaireScore);

        $orientationData = [
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId,
            'questionnaire_score' => (float)$questionnaireScore,
            'score' => $score,
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if record exists (should exist due to auto-population)
        $existing = $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $orientationData);
        } else {
            // Create if somehow missing
            return $this->insert($orientationData);
        }
    }

    /**
     * Recalculate scores that are currently 0
     */
    public function recalculateZeroScores($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return 0;
        }

        // Get all records with score = 0
        $recordsToUpdate = $this->where('semester_id', $semesterId)
            ->where('score', 0)
            ->findAll();

        $updatedCount = 0;

        foreach ($recordsToUpdate as $record) {
            // Calculate new score using database settings
            $newScore = $this->calculateOrientationScore($record['questionnaire_score']);

            // Update if score would be different from 0
            if ($newScore > 0) {
                $this->update($record['id'], [
                    'score' => $newScore,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Recalculate all scores for semester with better performance
     */
    public function recalculateAllScores($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return 0;
        }

        // Get all records for the semester
        $allRecords = $this->where('semester_id', $semesterId)->findAll();

        if (empty($allRecords)) {
            return 0;
        }

        $updatedCount = 0;

        // Use transaction for better performance
        $this->db->transStart();

        try {
            foreach ($allRecords as $record) {
                // Calculate new score using database settings
                $newScore = $this->calculateOrientationScore($record['questionnaire_score']);

                // Only update if score has changed
                if ((int)$record['score'] !== $newScore) {
                    $this->update($record['id'], [
                        'score' => $newScore,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $updatedCount++;
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $updatedCount;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in recalculateAllScores: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get orientation statistics for current semester
     */
    public function getOrientationStatistics($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [
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

        $data = $this->where('semester_id', $semesterId)->findAll();

        if (empty($data)) {
            return [
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

        $totalLecturers = count($data);
        $totalScore = 0;
        $totalQuestionnaireScore = 0;

        $scoreDistribution = [
            'excellent' => 0, // ≥88
            'good' => 0,      // 80-87
            'fair' => 0,      // 70-79
            'poor' => 0       // <70
        ];

        foreach ($data as $record) {
            $totalScore += (int)$record['score'];
            $totalQuestionnaireScore += (float)$record['questionnaire_score'];

            // Distribute overall scores
            $overallScore = (int)$record['score'];
            if ($overallScore >= 88) {
                $scoreDistribution['excellent']++;
            } elseif ($overallScore >= 80) {
                $scoreDistribution['good']++;
            } elseif ($overallScore >= 70) {
                $scoreDistribution['fair']++;
            } else {
                $scoreDistribution['poor']++;
            }
        }

        return [
            'average_score' => $totalLecturers > 0 ? round($totalScore / $totalLecturers, 1) : 0,
            'average_questionnaire_score' => $totalLecturers > 0 ? round($totalQuestionnaireScore / $totalLecturers, 2) : 0,
            'total_lecturers' => $totalLecturers,
            'score_distribution' => $scoreDistribution
        ];
    }

    /**
     * Get orientation data for specific lecturer and semester
     */
    public function getLecturerOrientation($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    /**
     * Check if lecturer has orientation data for semester
     */
    public function hasOrientationData($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->countAllResults() > 0;
    }

    /**
     * Get calculation summary for semester
     */
    public function getCalculationSummary($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [
                'total_records' => 0,
                'calculated_records' => 0,
                'zero_score_records' => 0,
                'last_calculation' => null
            ];
        }

        $totalRecords = $this->where('semester_id', $semesterId)->countAllResults();
        $zeroScoreRecords = $this->where('semester_id', $semesterId)
            ->where('score', 0)->countAllResults();

        $lastUpdated = $this->where('semester_id', $semesterId)
            ->where('updated_at IS NOT NULL')
            ->orderBy('updated_at', 'DESC')
            ->first();

        return [
            'total_records' => $totalRecords,
            'calculated_records' => $totalRecords - $zeroScoreRecords,
            'zero_score_records' => $zeroScoreRecords,
            'last_calculation' => $lastUpdated ? $lastUpdated['updated_at'] : null
        ];
    }
}
