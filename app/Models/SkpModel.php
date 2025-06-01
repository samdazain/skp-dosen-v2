<?php

namespace App\Models;

use CodeIgniter\Model;

class SKPModel extends Model
{
    protected $table = 'master_skp';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'total_score',
        'created_at'
    ];
    protected $useTimestamps = false; // We'll handle created_at manually

    protected $lecturerModel;

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
    }

    /**
     * Get SKP data with lecturer information and calculated component scores
     */
    public function getSKPDataWithLecturers($semesterId = null, $filters = [])
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [];
        }

        // Auto-populate SKP table with all lecturers for this semester
        $this->autoPopulateSKPData($semesterId);

        // Build the query to get all lecturers with their component scores
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                l.id as lecturer_id,
                l.nip,
                l.name as lecturer_name,
                l.position,
                l.study_program,
                COALESCE(i.score, 0) as integrity_score,
                COALESCE(d.score, 0) as discipline_score,
                COALESCE(cm.score, 0) as commitment_score,
                COALESCE(cp.score, 0) as cooperation_score,
                COALESCE(so.score, 0) as orientation_score,
                ms.total_score as skp_score,
                ms.id as skp_id,
                ms.created_at
            FROM lecturers l
            LEFT JOIN master_skp ms ON l.id = ms.lecturer_id AND ms.semester_id = ?
            LEFT JOIN integrity i ON l.id = i.lecturer_id AND i.semester_id = ?
            LEFT JOIN discipline d ON l.id = d.lecturer_id AND d.semester_id = ?
            LEFT JOIN commitment cm ON l.id = cm.lecturer_id AND cm.semester_id = ?
            LEFT JOIN cooperation cp ON l.id = cp.lecturer_id AND cp.semester_id = ?
            LEFT JOIN service_orientation so ON l.id = so.lecturer_id AND so.semester_id = ?
            WHERE 1 = 1
        ";

        $params = [$semesterId, $semesterId, $semesterId, $semesterId, $semesterId, $semesterId];

        // Apply filters
        if (!empty($filters['position'])) {
            $sql .= ' AND l.position = ?';
            $params[] = $filters['position'];
        }

        if (!empty($filters['study_program'])) {
            $sql .= ' AND l.study_program = ?';
            $params[] = $filters['study_program'];
        }

        $sql .= ' ORDER BY l.name ASC';

        $data = $db->query($sql, $params)->getResultArray();

        // Calculate component averages and SKP scores, determine categories
        foreach ($data as &$row) {
            $integrityScore = (int)$row['integrity_score'];
            $disciplineScore = (int)$row['discipline_score'];
            $commitmentScore = (int)$row['commitment_score'];
            $cooperationScore = (int)$row['cooperation_score'];
            $orientationScore = (int)$row['orientation_score'];

            // Calculate overall SKP score (average of all 5 components)
            $componentCount = 0;
            $totalScore = 0;

            if ($integrityScore > 0) {
                $totalScore += $integrityScore;
                $componentCount++;
            }
            if ($disciplineScore > 0) {
                $totalScore += $disciplineScore;
                $componentCount++;
            }
            if ($commitmentScore > 0) {
                $totalScore += $commitmentScore;
                $componentCount++;
            }
            if ($cooperationScore > 0) {
                $totalScore += $cooperationScore;
                $componentCount++;
            }
            if ($orientationScore > 0) {
                $totalScore += $orientationScore;
                $componentCount++;
            }

            // Calculate SKP score
            if ($componentCount > 0) {
                $skpScore = round($totalScore / $componentCount, 1);
            } else {
                $skpScore = 0;
            }

            // Update the row data
            $row['skp_score'] = $skpScore;
            $row['skp_category'] = $this->determineSKPCategory($skpScore);

            // Update database if needed
            if ($row['skp_id'] && (float)$row['skp_score'] !== (float)$skpScore) {
                $this->update($row['skp_id'], [
                    'total_score' => $skpScore
                ]);
            }
        }

        // Apply SKP category filter after calculation
        if (!empty($filters['skp_category'])) {
            $data = array_filter($data, function ($row) use ($filters) {
                return $row['skp_category'] === $filters['skp_category'];
            });
        }

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

        return array_values($data); // Re-index array after filtering and sorting
    }

    /**
     * Auto-populate master_skp table with all lecturers for given semester
     */
    public function autoPopulateSKPData($semesterId)
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
            // Check if SKP record exists for this lecturer and semester
            if (!in_array($lecturer['id'], $existingRecords)) {
                $newRecords[] = [
                    'lecturer_id' => $lecturer['id'],
                    'semester_id' => $semesterId,
                    'total_score' => 0, // Will be calculated from component tables
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $addedCount++;
            }
        }

        // Bulk insert for better performance
        if (!empty($newRecords)) {
            try {
                $this->insertBatch($newRecords);
                log_message('info', "Auto-populated {$addedCount} SKP records for semester {$semesterId}");
            } catch (\Exception $e) {
                log_message('error', 'Error auto-populating SKP data: ' . $e->getMessage());
                throw $e;
            }
        }

        return $addedCount;
    }

    /**
     * Calculate SKP scores by fetching data from related tables
     */
    public function calculateSKPScores($lecturerId, $semesterId)
    {
        $db = \Config\Database::connect();

        // Get scores from each component table
        $integrity = $db->query("SELECT score FROM integrity WHERE lecturer_id = ? AND semester_id = ?", [$lecturerId, $semesterId])->getRow();
        $discipline = $db->query("SELECT score FROM discipline WHERE lecturer_id = ? AND semester_id = ?", [$lecturerId, $semesterId])->getRow();
        $commitment = $db->query("SELECT score FROM commitment WHERE lecturer_id = ? AND semester_id = ?", [$lecturerId, $semesterId])->getRow();
        $cooperation = $db->query("SELECT score FROM cooperation WHERE lecturer_id = ? AND semester_id = ?", [$lecturerId, $semesterId])->getRow();
        $orientation = $db->query("SELECT score FROM service_orientation WHERE lecturer_id = ? AND semester_id = ?", [$lecturerId, $semesterId])->getRow();

        $scores = [
            'integrity_score' => $integrity ? (int)$integrity->score : 0,
            'discipline_score' => $discipline ? (int)$discipline->score : 0,
            'commitment_score' => $commitment ? (int)$commitment->score : 0,
            'cooperation_score' => $cooperation ? (int)$cooperation->score : 0,
            'orientation_score' => $orientation ? (int)$orientation->score : 0
        ];

        // Calculate overall SKP score (average of non-zero components)
        $nonZeroScores = array_filter($scores);
        $componentCount = count($nonZeroScores);

        if ($componentCount > 0) {
            $skpScore = round(array_sum($nonZeroScores) / $componentCount, 1);
        } else {
            $skpScore = 0;
        }

        // Determine category
        $category = $this->determineSKPCategory($skpScore);

        return [
            'component_scores' => $scores,
            'skp_score' => $skpScore,
            'skp_category' => $category,
            'components_calculated' => $componentCount
        ];
    }

    /**
     * Determine SKP category based on score
     */
    private function determineSKPCategory($score)
    {
        if ($score >= 88) return 'Sangat Baik';
        if ($score >= 76) return 'Baik';
        if ($score >= 61) return 'Cukup';
        if ($score > 0) return 'Kurang';
        return 'Belum Dinilai';
    }

    /**
     * Recalculate all SKP scores for semester
     */
    public function recalculateAllSKPScores($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return 0;
        }

        // Get all SKP records for the semester
        $allRecords = $this->where('semester_id', $semesterId)->findAll();

        if (empty($allRecords)) {
            return 0;
        }

        $updatedCount = 0;

        // Use transaction for better performance
        $this->db->transStart();

        try {
            foreach ($allRecords as $record) {
                // Calculate new scores from component tables
                $calculationResult = $this->calculateSKPScores($record['lecturer_id'], $semesterId);
                $newScore = $calculationResult['skp_score'];

                // Only update if score has changed
                if ((float)$record['total_score'] !== $newScore) {
                    $this->update($record['id'], [
                        'total_score' => $newScore
                    ]);
                    $updatedCount++;
                    log_message('debug', "Updated SKP score for lecturer {$record['lecturer_id']}: {$record['total_score']} -> {$newScore}");
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return $updatedCount;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in recalculateAllSKPScores: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get SKP statistics for current semester
     */
    public function getSKPStatistics($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return $this->getEmptyStatistics();
        }

        // Get data with calculated component scores
        $data = $this->getSKPDataWithLecturers($semesterId);

        if (empty($data)) {
            return $this->getEmptyStatistics();
        }

        $totalLecturers = count($data);
        $totalSKPScore = 0;
        $totalIntegrity = 0;
        $totalDiscipline = 0;
        $totalCommitment = 0;
        $totalCooperation = 0;
        $totalOrientation = 0;

        $categoryDistribution = [
            'sangat_baik' => 0,
            'baik' => 0,
            'cukup' => 0,
            'kurang' => 0
        ];

        foreach ($data as $record) {
            $totalSKPScore += (float)$record['skp_score'];
            $totalIntegrity += (int)$record['integrity_score'];
            $totalDiscipline += (int)$record['discipline_score'];
            $totalCommitment += (int)$record['commitment_score'];
            $totalCooperation += (int)$record['cooperation_score'];
            $totalOrientation += (int)$record['orientation_score'];

            // Count categories
            switch ($record['skp_category']) {
                case 'Sangat Baik':
                    $categoryDistribution['sangat_baik']++;
                    break;
                case 'Baik':
                    $categoryDistribution['baik']++;
                    break;
                case 'Cukup':
                    $categoryDistribution['cukup']++;
                    break;
                case 'Kurang':
                case 'Belum Dinilai':
                    $categoryDistribution['kurang']++;
                    break;
            }
        }

        return [
            'total_lecturers' => $totalLecturers,
            'average_skp_score' => $totalLecturers > 0 ? round($totalSKPScore / $totalLecturers, 1) : 0,
            'category_distribution' => $categoryDistribution,
            'component_averages' => [
                'integrity' => $totalLecturers > 0 ? round($totalIntegrity / $totalLecturers, 1) : 0,
                'discipline' => $totalLecturers > 0 ? round($totalDiscipline / $totalLecturers, 1) : 0,
                'commitment' => $totalLecturers > 0 ? round($totalCommitment / $totalLecturers, 1) : 0,
                'cooperation' => $totalLecturers > 0 ? round($totalCooperation / $totalLecturers, 1) : 0,
                'orientation' => $totalLecturers > 0 ? round($totalOrientation / $totalLecturers, 1) : 0
            ]
        ];
    }

    /**
     * Get empty statistics structure
     */
    private function getEmptyStatistics()
    {
        return [
            'total_lecturers' => 0,
            'average_skp_score' => 0,
            'category_distribution' => [
                'sangat_baik' => 0,
                'baik' => 0,
                'cukup' => 0,
                'kurang' => 0
            ],
            'component_averages' => [
                'integrity' => 0,
                'discipline' => 0,
                'commitment' => 0,
                'cooperation' => 0,
                'orientation' => 0
            ]
        ];
    }

    /**
     * Get detailed component breakdown for a lecturer
     */
    public function getLecturerComponentDetails($lecturerId, $semesterId)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                l.id as lecturer_id,
                l.name as lecturer_name,
                l.nip,
                l.position,
                l.study_program,
                i.score as integrity_score,
                i.teaching_attendance,
                i.courses_taught,
                d.score as discipline_score,
                d.daily_absence,
                d.exercise_morning_absence,
                d.ceremony_absence,
                cm.score as commitment_score,
                cm.competence,
                cm.tridharma_pass,
                cp.score as cooperation_score,
                cp.level as cooperation_level,
                so.score as orientation_score,
                so.questionnaire_score,
                ms.total_score as skp_score
            FROM lecturers l
            LEFT JOIN master_skp ms ON l.id = ms.lecturer_id AND ms.semester_id = ?
            LEFT JOIN integrity i ON l.id = i.lecturer_id AND i.semester_id = ?
            LEFT JOIN discipline d ON l.id = d.lecturer_id AND d.semester_id = ?
            LEFT JOIN commitment cm ON l.id = cm.lecturer_id AND cm.semester_id = ?
            LEFT JOIN cooperation cp ON l.id = cp.lecturer_id AND cp.semester_id = ?
            LEFT JOIN service_orientation so ON l.id = so.lecturer_id AND so.semester_id = ?
            WHERE l.id = ?
        ";

        $result = $db->query($sql, [
            $semesterId,
            $semesterId,
            $semesterId,
            $semesterId,
            $semesterId,
            $semesterId,
            $lecturerId
        ])->getRowArray();

        if ($result) {
            // Calculate the real-time SKP score
            $calculationResult = $this->calculateSKPScores($lecturerId, $semesterId);
            $result['calculated_skp_score'] = $calculationResult['skp_score'];
            $result['skp_category'] = $calculationResult['skp_category'];
            $result['components_calculated'] = $calculationResult['components_calculated'];
        }

        return $result;
    }

    /**
     * Get SKP data for specific lecturer and semester
     */
    public function getLecturerSKP($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    /**
     * Get component scores summary for dashboard/reports
     */
    public function getComponentScoresSummary($semesterId)
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                COUNT(DISTINCT l.id) as total_lecturers,
                AVG(COALESCE(i.score, 0)) as avg_integrity,
                AVG(COALESCE(d.score, 0)) as avg_discipline,
                AVG(COALESCE(cm.score, 0)) as avg_commitment,
                AVG(COALESCE(cp.score, 0)) as avg_cooperation,
                AVG(COALESCE(so.score, 0)) as avg_orientation,
                COUNT(CASE WHEN i.score > 0 THEN 1 END) as integrity_count,
                COUNT(CASE WHEN d.score > 0 THEN 1 END) as discipline_count,
                COUNT(CASE WHEN cm.score > 0 THEN 1 END) as commitment_count,
                COUNT(CASE WHEN cp.score > 0 THEN 1 END) as cooperation_count,
                COUNT(CASE WHEN so.score > 0 THEN 1 END) as orientation_count
            FROM lecturers l
            LEFT JOIN integrity i ON l.id = i.lecturer_id AND i.semester_id = ?
            LEFT JOIN discipline d ON l.id = d.lecturer_id AND d.semester_id = ?
            LEFT JOIN commitment cm ON l.id = cm.lecturer_id AND cm.semester_id = ?
            LEFT JOIN cooperation cp ON l.id = cp.lecturer_id AND cp.semester_id = ?
            LEFT JOIN service_orientation so ON l.id = so.lecturer_id AND so.semester_id = ?
        ";

        return $db->query($sql, [
            $semesterId,
            $semesterId,
            $semesterId,
            $semesterId,
            $semesterId
        ])->getRowArray();
    }
}
