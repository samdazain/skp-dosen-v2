<?php

namespace App\Models;

use CodeIgniter\Model;

class CooperationModel extends Model
{
    protected $table = 'cooperation';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'level',
        'score',
        'updated_by'
    ];

    protected $useTimestamps = false; // Disable automatic timestamps since we only have updated_at
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'lecturer_id' => 'required|integer',
        'semester_id' => 'required|integer',
        'level' => 'required|in_list[not_cooperative,fair,cooperative,very_cooperative]'
    ];

    protected $validationMessages = [
        'lecturer_id' => [
            'required' => 'Lecturer ID is required',
            'integer' => 'Lecturer ID must be an integer'
        ],
        'semester_id' => [
            'required' => 'Semester ID is required',
            'integer' => 'Semester ID must be an integer'
        ],
        'level' => [
            'required' => 'Cooperation level is required',
            'in_list' => 'Cooperation level must be valid'
        ]
    ];

    protected $lecturerModel;
    protected $scoreModel;

    protected $beforeInsert = ['calculateScoreBeforeInsert'];
    protected $beforeUpdate = ['calculateScoreBeforeUpdate'];

    public function __construct()
    {
        parent::__construct();
        $this->lecturerModel = new \App\Models\LecturerModel();
        $this->scoreModel = new \App\Models\ScoreModel();
    }

    /**
     * Automatically calculate score before insert
     */
    protected function calculateScoreBeforeInsert(array $data)
    {
        if (isset($data['data']['level'])) {
            $level = $data['data']['level'];
            $data['data']['score'] = $this->calculateCooperationScore($level);

            log_message('info', "Auto-calculated cooperation score before insert: {$data['data']['score']} for level: {$level}");
        }

        return $data;
    }

    /**
     * Automatically calculate score before update
     */
    protected function calculateScoreBeforeUpdate(array $data)
    {
        if (isset($data['data']['level'])) {
            $level = $data['data']['level'];
            $data['data']['score'] = $this->calculateCooperationScore($level);

            log_message('info', "Auto-calculated cooperation score before update: {$data['data']['score']} for level: {$level}");
        }

        return $data;
    }

    /**
     * Get all lecturers with their cooperation data for a specific semester with filtering
     */
    public function getAllLecturersWithCooperation($semesterId, $filters = [])
    {
        // Auto-populate cooperation data first
        try {
            $addedCount = $this->autoPopulateCooperationData($semesterId);
            if ($addedCount > 0) {
                log_message('info', "Auto-populated {$addedCount} new cooperation records");
            }
        } catch (\Exception $e) {
            log_message('error', 'Error during cooperation auto-population: ' . $e->getMessage());
        }

        // Use LecturerModel to ensure we get all lecturers
        $lecturers = $this->lecturerModel->findAll();

        if (empty($lecturers)) {
            log_message('warning', 'No lecturers found in database');
            return [];
        }

        $db = \Config\Database::connect();

        $sql = '
            SELECT 
                l.id as lecturer_id,
                l.name as lecturer_name,
                l.nip,
                l.position,
                l.study_program,
                COALESCE(c.level, "not_cooperative") as level,
                COALESCE(c.score, 0) as score,
                c.id as cooperation_id,
                c.updated_at,
                c.updated_by
            FROM lecturers l
            LEFT JOIN cooperation c ON l.id = c.lecturer_id AND c.semester_id = ?
            WHERE 1 = 1';

        $params = [$semesterId];

        // Apply filters
        if (!empty($filters['position'])) {
            $sql .= ' AND l.position = ?';
            $params[] = $filters['position'];
        }

        if (!empty($filters['study_program'])) {
            $sql .= ' AND l.study_program = ?';
            $params[] = $filters['study_program'];
        }

        if (isset($filters['level']) && $filters['level'] !== '') {
            $sql .= ' AND COALESCE(c.level, "not_cooperative") = ?';
            $params[] = $filters['level'];
        }

        $sql .= ' ORDER BY l.name ASC';

        log_message('debug', "Executing cooperation query with semester_id: {$semesterId}");

        $result = $db->query($sql, $params)->getResultArray();

        log_message('info', "Retrieved " . count($result) . " lecturer records with cooperation data");

        return $result;
    }

    /**
     * Auto-populate cooperation table with all lecturers for a specific semester
     */
    public function autoPopulateCooperationData($semesterId)
    {
        try {
            log_message('info', "Starting cooperation auto-population for semester: {$semesterId}");

            // Validate semester ID
            if (empty($semesterId) || !is_numeric($semesterId)) {
                log_message('error', "Invalid semester ID: {$semesterId}");
                return 0;
            }

            // Check if semester exists
            $semesterModel = new \App\Models\SemesterModel();
            $semester = $semesterModel->find($semesterId);
            if (!$semester) {
                log_message('error', "Semester with ID {$semesterId} not found");
                return 0;
            }

            // Get all lecturers
            $db = \Config\Database::connect();
            $lecturersQuery = $db->query("SELECT id, name FROM lecturers ORDER BY name ASC");
            $lecturers = $lecturersQuery->getResultArray();

            if (empty($lecturers)) {
                log_message('warning', 'No lecturers found in database');
                return 0;
            }

            // Check existing cooperation records for this semester
            $existingQuery = $db->query("SELECT lecturer_id FROM cooperation WHERE semester_id = ?", [$semesterId]);
            $existingRecords = array_column($existingQuery->getResultArray(), 'lecturer_id');

            $newRecords = [];
            $addedCount = 0;

            foreach ($lecturers as $lecturer) {
                if (!in_array($lecturer['id'], $existingRecords)) {
                    // Auto-calculate score for default values
                    $defaultScore = $this->calculateCooperationScore('not_cooperative');

                    $newRecord = [
                        'lecturer_id' => (int)$lecturer['id'],
                        'semester_id' => (int)$semesterId,
                        'level' => 'not_cooperative',
                        'score' => $defaultScore,
                        'updated_by' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                        // Remove created_at since the table doesn't have this column
                    ];

                    $newRecords[] = $newRecord;
                    $addedCount++;

                    log_message('debug', "Prepared new cooperation record for lecturer ID: {$lecturer['id']} with score: {$defaultScore}");
                }
            }

            // Insert records
            if (!empty($newRecords)) {
                try {
                    $insertResult = $this->insertBatch($newRecords);
                    if ($insertResult) {
                        log_message('info', "Successfully bulk inserted {$addedCount} cooperation records");
                    } else {
                        log_message('warning', "Bulk insert returned false, trying individual inserts");

                        $individualSuccess = 0;
                        foreach ($newRecords as $record) {
                            try {
                                $result = $this->insert($record);
                                if ($result) {
                                    $individualSuccess++;
                                }
                            } catch (\Exception $e) {
                                log_message('error', "Exception inserting cooperation record for lecturer {$record['lecturer_id']}: " . $e->getMessage());
                            }
                        }
                        $addedCount = $individualSuccess;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Exception during cooperation insert: ' . $e->getMessage());
                    throw $e;
                }
            }

            log_message('info', "Cooperation auto-population completed: Added {$addedCount} records");
            return $addedCount;
        } catch (\Exception $e) {
            log_message('error', 'Error in autoPopulateCooperationData: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate cooperation score based on level
     * ALWAYS uses database values - no fallbacks
     * Enhanced to properly format values for score lookup
     */
    public function calculateCooperationScore($level)
    {
        try {
            // Validate and normalize the cooperation level
            $validLevels = [
                'not_cooperative' => 'not_cooperative',
                'fair' => 'fair',
                'cooperative' => 'cooperative',
                'very_cooperative' => 'very_cooperative'
            ];

            // Normalize the level value
            $normalizedLevel = strtolower(trim($level));

            if (!isset($validLevels[$normalizedLevel])) {
                throw new \Exception("Invalid cooperation level: {$level}");
            }

            $levelValue = $validLevels[$normalizedLevel];

            // Get score from database via ScoreModel using correct subcategory name
            $score = $this->scoreModel->getScoreForValue('cooperation', 'cooperation_level', $levelValue);

            log_message('debug', "Cooperation score from database for level '{$levelValue}': {$score}");

            return (int)$score;
        } catch (\Exception $e) {
            log_message('error', "Error calculating cooperation score for level='{$level}': " . $e->getMessage());
            throw new \Exception("Gagal menghitung skor kerjasama: " . $e->getMessage());
        }
    }

    public function canUpdateLecturerCooperation($lecturerId)
    {
        helper('role');

        // Get lecturer details
        $lecturer = $this->lecturerModel->find($lecturerId);
        if (!$lecturer) {
            return false;
        }

        return can_update_lecturer_score($lecturer['study_program']);
    }


    /**
     * Update cooperation level with role-based access control
     */
    public function updateCooperationLevel($lecturerId, $semesterId, $level, $updatedBy = null)
    {
        // Check permissions first
        if (!$this->canUpdateLecturerCooperation($lecturerId)) {
            // Get lecturer info for debug
            $lecturer = $this->lecturerModel->find($lecturerId);
            $lecturerStudyProgram = $lecturer ? $lecturer['study_program'] : 'unknown';

            $debugInfo = [
                'lecturer_id' => $lecturerId,
                'lecturer_study_program' => $lecturerStudyProgram,
                'user_role' => session()->get('user_role'),
                'user_study_program' => session()->get('user_study_program'),
                'reason' => 'Role-based access control failed'
            ];

            log_message('warning', 'Cooperation access denied: ' . json_encode($debugInfo));
            throw new \Exception('Anda tidak memiliki akses untuk mengubah data dosen ini');
        }

        log_message('info', "Starting updateCooperationLevel for lecturer {$lecturerId}, semester {$semesterId}, level: {$level}");

        // Validate level
        $validLevels = ['not_cooperative', 'fair', 'cooperative', 'very_cooperative'];
        if (!in_array($level, $validLevels)) {
            throw new \Exception("Invalid cooperation level: {$level}");
        }

        // Get current record
        $current = $this->where([
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId
        ])->first();

        // Calculate new score using database values
        try {
            $newScore = $this->calculateCooperationScore($level);
        } catch (\Exception $e) {
            log_message('error', 'Error calculating cooperation score: ' . $e->getMessage());
            throw new \Exception('Gagal menghitung skor kerjasama: ' . $e->getMessage());
        }

        log_message('info', "Calculated cooperation score: {$newScore} for level: {$level}");

        // Use transaction to ensure data consistency
        $this->db->transStart();

        try {
            // Prepare update data
            $updateData = [
                'level' => $level,
                'score' => $newScore,
                'updated_by' => $updatedBy,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($current) {
                $result = $this->update($current['id'], $updateData);
            } else {
                // Create new record if doesn't exist
                $updateData['lecturer_id'] = $lecturerId;
                $updateData['semester_id'] = $semesterId;
                $result = $this->insert($updateData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false || !$result) {
                throw new \Exception('Database update failed');
            }

            // Verify the update by checking the database
            $updatedRecord = $this->where([
                'lecturer_id' => $lecturerId,
                'semester_id' => $semesterId
            ])->first();

            if (!$updatedRecord || (int)$updatedRecord['score'] !== $newScore) {
                throw new \Exception('Score verification failed after update');
            }

            log_message('info', "updateCooperationLevel completed successfully. Database score: {$updatedRecord['score']}");

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in updateCooperationLevel transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get cooperation statistics for a specific semester
     */
    public function getCooperationStats($semesterId)
    {
        $allLecturers = $this->getAllLecturersWithCooperation($semesterId);
        $totalLecturers = count($allLecturers);

        $levelCounts = [
            'very_cooperative' => 0,
            'cooperative' => 0,
            'fair' => 0,
            'not_cooperative' => 0
        ];

        foreach ($allLecturers as $lecturer) {
            $level = $lecturer['level'] ?? 'not_cooperative';
            if (isset($levelCounts[$level])) {
                $levelCounts[$level]++;
            }
        }

        // Calculate percentages
        $levelPercentages = [];
        foreach ($levelCounts as $level => $count) {
            $levelPercentages[$level] = $totalLecturers > 0 ? round(($count / $totalLecturers) * 100, 1) : 0;
        }

        return [
            'total_lecturers' => $totalLecturers,
            'level_counts' => $levelCounts,
            'level_percentages' => $levelPercentages,
            'very_cooperative_percentage' => $levelPercentages['very_cooperative'],
            'cooperative_percentage' => $levelPercentages['cooperative'],
            'fair_percentage' => $levelPercentages['fair'],
            'not_cooperative_percentage' => $levelPercentages['not_cooperative']
        ];
    }

    /**
     * Refresh cooperation data for a semester
     */
    public function refreshCooperationData($semesterId)
    {
        try {
            log_message('info', "Starting cooperation data refresh for semester: {$semesterId}");

            // Auto-populate missing lecturer records
            $addedCount = $this->autoPopulateCooperationData($semesterId);

            // Recalculate scores for zero-score records
            $recalculatedCount = $this->recalculateZeroScores($semesterId);

            log_message('info', "Cooperation data refresh completed - Added: {$addedCount}, Recalculated: {$recalculatedCount}");
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error in refreshCooperationData: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate scores that are currently 0 using database values - no fallbacks
     */
    public function recalculateZeroScores($semesterId)
    {
        $recordsToUpdate = $this->where('semester_id', $semesterId)
            ->where('score', 0)
            ->findAll();

        $updatedCount = 0;

        foreach ($recordsToUpdate as $record) {
            $newScore = $this->calculateCooperationScore($record['level']);

            $this->update($record['id'], [
                'score' => $newScore,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Get cooperation level display text
     */
    public function getLevelDisplayText($level)
    {
        $levelTexts = [
            'very_cooperative' => 'Sangat Kooperatif',
            'cooperative' => 'Kooperatif',
            'fair' => 'Cukup Kooperatif',
            'not_cooperative' => 'Tidak Kooperatif'
        ];

        return $levelTexts[$level] ?? 'Tidak Diketahui';
    }

    /**
     * Get cooperation level value for display
     */
    public function getLevelValue($level)
    {
        $levelValues = [
            'very_cooperative' => 90,
            'cooperative' => 80,
            'fair' => 70,
            'not_cooperative' => 60
        ];

        return $levelValues[$level] ?? 60;
    }
}
