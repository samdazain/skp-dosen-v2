<?php

namespace App\Models;

use CodeIgniter\Model;

class DisciplineModel extends Model
{
    protected $table = 'discipline';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'daily_absence',
        'exercise_morning_absence',
        'ceremony_absence',
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
     * Get discipline data with lecturer information for current semester
     * Auto-populate missing lecturers and include position ordering
     */
    public function getDisciplineDataWithLecturers($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [];
        }

        // Auto-populate discipline table with all lecturers for this semester
        $this->autoPopulateDisciplineData($semesterId);

        // Auto-recalculate scores that are 0
        $this->recalculateZeroScores($semesterId);

        // Get the data with lecturer information, ordered by position hierarchy
        $data = $this->select('
                discipline.*,
                lecturers.nip,
                lecturers.name as lecturer_name,
                lecturers.position,
                lecturers.study_program
            ')
            ->join('lecturers', 'lecturers.id = discipline.lecturer_id')
            ->where('discipline.semester_id', $semesterId)
            ->findAll();

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
     * Auto-populate discipline table with all lecturers for given semester
     * Enhanced with better performance
     */
    public function autoPopulateDisciplineData($semesterId)
    {
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
            // Check if discipline record exists for this lecturer and semester
            if (!in_array($lecturer['id'], $existingRecords)) {
                $newRecords[] = [
                    'lecturer_id' => $lecturer['id'],
                    'semester_id' => $semesterId,
                    'daily_absence' => 0, // Default: no absences
                    'exercise_morning_absence' => 0, // Default: no absences
                    'ceremony_absence' => 0, // Default: no absences
                    'score' => 0, // Will be calculated when real data is entered
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
                log_message('info', "Auto-populated {$addedCount} discipline records for semester {$semesterId}");
            } catch (\Exception $e) {
                log_message('error', 'Error auto-populating discipline data: ' . $e->getMessage());
                throw $e;
            }
        }

        return $addedCount;
    }

    /**
     * Calculate discipline score based on absence counts
     */
    public function calculateDisciplineScore($dailyAbsence, $exerciseAbsence, $ceremonyAbsence)
    {
        // Get scores from score settings using correct subcategory names
        $dailyScore = $this->scoreModel->calculateScore('discipline', 'daily_attendance', (int)$dailyAbsence);
        $exerciseScore = $this->scoreModel->calculateScore('discipline', 'morning_exercise', (int)$exerciseAbsence);
        $ceremonyScore = $this->scoreModel->calculateScore('discipline', 'ceremony_attendance', (int)$ceremonyAbsence);

        // Calculate weighted average: daily (60%), exercise (20%), ceremony (20%)
        $totalScore = ($dailyScore * 0.6) + ($exerciseScore * 0.2) + ($ceremonyScore * 0.2);

        return (int)round($totalScore);
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
            return false;
        }

        // Get all records with score = 0
        $recordsToUpdate = $this->where('semester_id', $semesterId)
            ->where('score', 0)
            ->findAll();

        $updatedCount = 0;

        foreach ($recordsToUpdate as $record) {
            // Calculate new score
            $newScore = $this->calculateDisciplineScore(
                $record['daily_absence'],
                $record['exercise_morning_absence'],
                $record['ceremony_absence']
            );

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
                // Calculate new score
                $newScore = $this->calculateDisciplineScore(
                    $record['daily_absence'],
                    $record['exercise_morning_absence'],
                    $record['ceremony_absence']
                );

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
     * Update discipline data and recalculate score
     */
    public function updateLecturerDiscipline($lecturerId, $semesterId, $data, $updatedBy = null)
    {
        // Calculate score
        $score = $this->calculateDisciplineScore(
            $data['daily_absence'],
            $data['exercise_morning_absence'],
            $data['ceremony_absence']
        );

        $disciplineData = [
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId,
            'daily_absence' => (int)$data['daily_absence'],
            'exercise_morning_absence' => (int)$data['exercise_morning_absence'],
            'ceremony_absence' => (int)$data['ceremony_absence'],
            'score' => $score,
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if record exists (should exist due to auto-population)
        $existing = $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $disciplineData);
        } else {
            // Create if somehow missing
            return $this->insert($disciplineData);
        }
    }

    /**
     * Get discipline statistics for current semester
     */
    public function getDisciplineStatistics($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [
                'average_daily_score' => 0,
                'average_exercise_score' => 0,
                'average_ceremony_score' => 0,
                'total_lecturers' => 0,
                'daily' => $this->getEmptyDistribution(),
                'exercise' => $this->getEmptyDistribution(),
                'ceremony' => $this->getEmptyDistribution(),
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
                'average_daily_score' => 0,
                'average_exercise_score' => 0,
                'average_ceremony_score' => 0,
                'total_lecturers' => 0,
                'daily' => $this->getEmptyDistribution(),
                'exercise' => $this->getEmptyDistribution(),
                'ceremony' => $this->getEmptyDistribution(),
                'score_distribution' => [
                    'excellent' => 0,
                    'good' => 0,
                    'fair' => 0,
                    'poor' => 0
                ]
            ];
        }

        $totalLecturers = count($data);
        $totalDailyScore = 0;
        $totalExerciseScore = 0;
        $totalCeremonyScore = 0;

        // Initialize counters for absence distribution
        $dailyDistribution = $this->getEmptyDistribution();
        $exerciseDistribution = $this->getEmptyDistribution();
        $ceremonyDistribution = $this->getEmptyDistribution();

        $scoreDistribution = [
            'excellent' => 0, // 90-100
            'good' => 0,      // 80-89
            'fair' => 0,      // 70-79
            'poor' => 0       // <70
        ];

        foreach ($data as $record) {
            // Calculate individual component scores using correct subcategory names
            $dailyScore = $this->scoreModel->calculateScore('discipline', 'daily_attendance', $record['daily_absence']);
            $exerciseScore = $this->scoreModel->calculateScore('discipline', 'morning_exercise', $record['exercise_morning_absence']);
            $ceremonyScore = $this->scoreModel->calculateScore('discipline', 'ceremony_attendance', $record['ceremony_absence']);

            $totalDailyScore += $dailyScore;
            $totalExerciseScore += $exerciseScore;
            $totalCeremonyScore += $ceremonyScore;

            // Count absence distributions
            $this->countAbsenceDistribution($record['daily_absence'], $dailyDistribution);
            $this->countAbsenceDistribution($record['exercise_morning_absence'], $exerciseDistribution);
            $this->countAbsenceDistribution($record['ceremony_absence'], $ceremonyDistribution);

            // Distribute overall scores
            $overallScore = (int)$record['score'];
            if ($overallScore >= 90) {
                $scoreDistribution['excellent']++;
            } elseif ($overallScore >= 80) {
                $scoreDistribution['good']++;
            } elseif ($overallScore >= 70) {
                $scoreDistribution['fair']++;
            } else {
                $scoreDistribution['poor']++;
            }
        }

        // Calculate percentages
        $dailyDistribution = $this->calculatePercentages($dailyDistribution, $totalLecturers);
        $exerciseDistribution = $this->calculatePercentages($exerciseDistribution, $totalLecturers);
        $ceremonyDistribution = $this->calculatePercentages($ceremonyDistribution, $totalLecturers);

        return [
            'average_daily_score' => $totalLecturers > 0 ? round($totalDailyScore / $totalLecturers, 1) : 0,
            'average_exercise_score' => $totalLecturers > 0 ? round($totalExerciseScore / $totalLecturers, 1) : 0,
            'average_ceremony_score' => $totalLecturers > 0 ? round($totalCeremonyScore / $totalLecturers, 1) : 0,
            'total_lecturers' => $totalLecturers,
            'daily' => $dailyDistribution,
            'exercise' => $exerciseDistribution,
            'ceremony' => $ceremonyDistribution,
            'score_distribution' => $scoreDistribution
        ];
    }

    /**
     * Get empty distribution array
     */
    private function getEmptyDistribution()
    {
        return [
            'count_no_alpha' => 0,
            'count_1_2_alpha' => 0,
            'count_3_4_alpha' => 0,
            'count_above_5_alpha' => 0,
            'total' => 0,
            'percentage_no_alpha' => 0,
            'percentage_1_2_alpha' => 0,
            'percentage_3_4_alpha' => 0,
            'percentage_above_5_alpha' => 0
        ];
    }

    /**
     * Count absence distribution
     */
    private function countAbsenceDistribution($absenceCount, &$distribution)
    {
        $distribution['total']++;

        if ($absenceCount == 0) {
            $distribution['count_no_alpha']++;
        } elseif ($absenceCount >= 1 && $absenceCount <= 2) {
            $distribution['count_1_2_alpha']++;
        } elseif ($absenceCount >= 3 && $absenceCount <= 4) {
            $distribution['count_3_4_alpha']++;
        } else {
            $distribution['count_above_5_alpha']++;
        }
    }

    /**
     * Calculate percentages for distribution
     */
    private function calculatePercentages($distribution, $total)
    {
        if ($total > 0) {
            $distribution['percentage_no_alpha'] = round(($distribution['count_no_alpha'] / $total) * 100, 1);
            $distribution['percentage_1_2_alpha'] = round(($distribution['count_1_2_alpha'] / $total) * 100, 1);
            $distribution['percentage_3_4_alpha'] = round(($distribution['count_3_4_alpha'] / $total) * 100, 1);
            $distribution['percentage_above_5_alpha'] = round(($distribution['count_above_5_alpha'] / $total) * 100, 1);
        }

        return $distribution;
    }

    /**
     * Bulk update discipline data from uploaded file
     */
    public function bulkUpdateDiscipline($disciplineData, $semesterId, $updatedBy = null)
    {
        $this->db->transStart();

        // First, ensure all lecturers are populated
        $this->autoPopulateDisciplineData($semesterId);

        foreach ($disciplineData as $data) {
            // Find lecturer by NIP
            $lecturer = $this->lecturerModel->where('nip', $data['nip'])->first();

            if ($lecturer) {
                $this->updateLecturerDiscipline(
                    $lecturer['id'],
                    $semesterId,
                    [
                        'daily_absence' => $data['daily_absence'],
                        'exercise_morning_absence' => $data['exercise_morning_absence'],
                        'ceremony_absence' => $data['ceremony_absence']
                    ],
                    $updatedBy
                );
            }
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Get discipline data for specific lecturer and semester
     */
    public function getLecturerDiscipline($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    /**
     * Check if lecturer has discipline data for semester
     */
    public function hasDisciplineData($lecturerId, $semesterId)
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
