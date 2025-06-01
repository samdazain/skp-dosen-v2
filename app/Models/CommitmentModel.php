<?php

namespace App\Models;

use CodeIgniter\Model;

class CommitmentModel extends Model
{
    protected $table = 'commitment';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'competence',
        'tridharma_pass',
        'score',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'lecturer_id' => 'required|integer',
        'semester_id' => 'required|integer',
        'competence' => 'required|in_list[active,inactive]',
        'tridharma_pass' => 'required|in_list[0,1]'
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
        'competence' => [
            'required' => 'Competence status is required',
            'in_list' => 'Competence must be active or inactive'
        ],
        'tridharma_pass' => [
            'required' => 'Tri Dharma status is required',
            'in_list' => 'Tri Dharma status must be 0 or 1'
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

    public function getCommitmentDataBySemester($semesterId)
    {
        return $this->select('
                commitment.id,
                commitment.lecturer_id,
                commitment.semester_id,
                commitment.competence,
                commitment.tridharma_pass,
                commitment.score,
                commitment.updated_at,
                lecturers.name as lecturer_name,
                lecturers.nip,
                lecturers.position,
                lecturers.study_program
            ')
            ->join('lecturers', 'lecturers.id = commitment.lecturer_id')
            ->where('commitment.semester_id', $semesterId)
            ->orderBy('lecturers.name', 'ASC')
            ->findAll();
    }

    public function getAllLecturersWithCommitmentBasic($semesterId)
    {
        $db = \Config\Database::connect();

        return $db->query('
            SELECT 
                l.id as lecturer_id,
                l.name as lecturer_name,
                l.nip,
                l.position,
                l.study_program,
                COALESCE(c.competence, "inactive") as competence,
                COALESCE(c.tridharma_pass, 0) as tridharma_pass,
                COALESCE(c.score, 0) as score,
                c.id as commitment_id,
                c.updated_at
            FROM lecturers l
            LEFT JOIN commitment c ON l.id = c.lecturer_id AND c.semester_id = ?
            ORDER BY l.name ASC
        ', [$semesterId])->getResultArray();
    }

    /**
     * Get all lecturers with their commitment data for a specific semester with filtering
     * Enhanced to ensure data is always available and auto-recalculate scores
     */
    public function getAllLecturersWithCommitment($semesterId, $filters = [])
    {
        // ALWAYS ensure auto-population happens first
        try {
            $addedCount = $this->autoPopulateCommitmentData($semesterId);
            if ($addedCount > 0) {
                log_message('info', "Auto-populated {$addedCount} new commitment records");
            }

            // Auto-recalculate ALL scores similar to DisciplineModel
            $updatedCount = $this->recalculateAllScores($semesterId);
            if ($updatedCount > 0) {
                log_message('info', "Auto-recalculated {$updatedCount} commitment scores");
            }
        } catch (\Exception $e) {
            log_message('error', 'Error during auto-population and score calculation: ' . $e->getMessage());
            // Continue anyway to show existing data
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
                COALESCE(c.competence, "inactive") as competence,
                COALESCE(c.tridharma_pass, 0) as tridharma_pass,
                COALESCE(c.score, 0) as score,
                c.id as commitment_id,
                c.updated_at,
                c.updated_by
            FROM lecturers l
            LEFT JOIN commitment c ON l.id = c.lecturer_id AND c.semester_id = ?
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

        if (isset($filters['competence']) && $filters['competence'] !== '') {
            $sql .= ' AND COALESCE(c.competence, "inactive") = ?';
            $params[] = $filters['competence'];
        }

        if (isset($filters['tridharma']) && $filters['tridharma'] !== '') {
            $sql .= ' AND COALESCE(c.tridharma_pass, 0) = ?';
            $params[] = (int)$filters['tridharma'];
        }

        $sql .= ' ORDER BY l.name ASC';

        log_message('debug', "Executing query with semester_id: {$semesterId}");
        log_message('debug', "SQL: " . $sql);

        $result = $db->query($sql, $params)->getResultArray();

        log_message('info', "Retrieved " . count($result) . " lecturer records with commitment data");

        return $result;
    }

    /**
     * Calculate commitment score based on competence and tri dharma status
     * ALWAYS uses database values - no fallbacks
     * Enhanced to properly format values for score lookup
     */
    public function calculateCommitmentScore($competence, $triDharmaPass)
    {
        try {
            // Normalize competence value for score lookup
            $competenceValue = ($competence === 'active' || $competence === true || $competence === '1') ? 'active' : 'inactive';

            // Normalize tri dharma value for score lookup
            $triDharmaValue = ($triDharmaPass === true || $triDharmaPass === '1' || $triDharmaPass === 1) ? 'pass' : 'fail';

            // Get scores from database via ScoreModel using proper subcategory names
            $competenceScore = $this->scoreModel->getScoreForValue('commitment', 'competency', $competenceValue);
            $triDharmaScore = $this->scoreModel->getScoreForValue('commitment', 'tri_dharma', $triDharmaValue);

            // Calculate average (50% each component)
            $totalScore = round(($competenceScore + $triDharmaScore) / 2);

            log_message('debug', "Commitment score calculation - Competency ('{$competenceValue}'): {$competenceScore}, TriDharma ('{$triDharmaValue}'): {$triDharmaScore}, Average: {$totalScore}");

            return (int)$totalScore;
        } catch (\Exception $e) {
            log_message('error', "Error calculating commitment score for competence='{$competence}', tridharma='{$triDharmaPass}': " . $e->getMessage());
            throw new \Exception("Gagal menghitung skor komitmen: " . $e->getMessage());
        }
    }

    /**
     * Calculate average score for commitment data using database values
     */
    public function calculateAverageScore($competence, $triDharmaPass)
    {
        return $this->calculateCommitmentScore($competence, $triDharmaPass);
    }

    /**
     * Update competency status with immediate score calculation and database update
     * Enhanced with proper verification
     */
    public function updateCompetency($lecturerId, $semesterId, $competence, $updatedBy = null)
    {
        log_message('info', "Starting updateCompetency for lecturer {$lecturerId}, semester {$semesterId}, competence: {$competence}");

        $competenceValue = ($competence === 'active' || $competence === true || $competence === '1') ? 'active' : 'inactive';

        // Get current record to preserve tri dharma status
        $current = $this->where([
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId
        ])->first();

        $triDharmaPass = $current ? $current['tridharma_pass'] : 0;

        // Calculate new score using database values - throw exception on error
        try {
            $newScore = $this->calculateCommitmentScore($competenceValue, $triDharmaPass);
        } catch (\Exception $e) {
            log_message('error', 'Error calculating commitment score: ' . $e->getMessage());
            throw new \Exception('Gagal menghitung skor komitmen: ' . $e->getMessage());
        }

        log_message('info', "Calculated new commitment score: {$newScore} for competence: {$competenceValue}, tridharma: {$triDharmaPass}");

        // Use transaction to ensure data consistency
        $this->db->transStart();

        try {
            // Prepare update data
            $updateData = [
                'competence' => $competenceValue,
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
                $updateData['tridharma_pass'] = 0;
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

            log_message('info', "updateCompetency completed successfully. Database score: {$updatedRecord['score']}");

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in updateCompetency transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update Tri Dharma status with immediate score calculation and database update
     * Enhanced with proper verification
     */
    public function updateTriDharma($lecturerId, $semesterId, $triDharmaPass, $updatedBy = null)
    {
        log_message('info', "Starting updateTriDharma for lecturer {$lecturerId}, semester {$semesterId}, tridharma: {$triDharmaPass}");

        $triDharmaValue = ($triDharmaPass === true || $triDharmaPass === '1' || $triDharmaPass === 1) ? 1 : 0;

        // Get current record to preserve competence status
        $current = $this->where([
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId
        ])->first();

        $competence = $current ? $current['competence'] : 'inactive';

        // Calculate new score using database values - throw exception on error
        try {
            $newScore = $this->calculateCommitmentScore($competence, $triDharmaValue);
        } catch (\Exception $e) {
            log_message('error', 'Error calculating commitment score: ' . $e->getMessage());
            throw new \Exception('Gagal menghitung skor komitmen: ' . $e->getMessage());
        }

        log_message('info', "Calculated new commitment score: {$newScore} for competence: {$competence}, tridharma: {$triDharmaValue}");

        // Use transaction to ensure data consistency
        $this->db->transStart();

        try {
            // Prepare update data
            $updateData = [
                'tridharma_pass' => $triDharmaValue,
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
                $updateData['competence'] = 'inactive';
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

            log_message('info', "updateTriDharma completed successfully. Database score: {$updatedRecord['score']}");

            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in updateTriDharma transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Automatically calculate score before insert - no fallbacks
     */
    protected function calculateScoreBeforeInsert(array $data)
    {
        if (isset($data['data']['competence']) || isset($data['data']['tridharma_pass'])) {
            $competence = $data['data']['competence'] ?? 'inactive';
            $triDharmaPass = $data['data']['tridharma_pass'] ?? 0;

            $data['data']['score'] = $this->calculateCommitmentScore($competence, $triDharmaPass);

            log_message('info', "Auto-calculated score before insert: {$data['data']['score']} for competence: {$competence}, tridharma: {$triDharmaPass}");
        }

        return $data;
    }

    /**
     * Automatically calculate score before update - no fallbacks
     */
    protected function calculateScoreBeforeUpdate(array $data)
    {
        if (isset($data['data']['competence']) || isset($data['data']['tridharma_pass'])) {
            // If only one field is being updated, get the other from database
            if (isset($data['id'])) {
                $existing = $this->find($data['id'][0]);

                $competence = $data['data']['competence'] ?? ($existing ? $existing['competence'] : 'inactive');
                $triDharmaPass = $data['data']['tridharma_pass'] ?? ($existing ? $existing['tridharma_pass'] : 0);
            } else {
                $competence = $data['data']['competence'] ?? 'inactive';
                $triDharmaPass = $data['data']['tridharma_pass'] ?? 0;
            }

            $data['data']['score'] = $this->calculateCommitmentScore($competence, $triDharmaPass);

            log_message('info', "Auto-calculated score before update: {$data['data']['score']} for competence: {$competence}, tridharma: {$triDharmaPass}");
        }

        return $data;
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
            $newScore = $this->calculateCommitmentScore($record['competence'], $record['tridharma_pass']);

            $this->update($record['id'], [
                'score' => $newScore,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Auto-populate commitment table with all lecturers for a specific semester
     * Enhanced version with comprehensive debugging and error handling
     */
    public function autoPopulateCommitmentData($semesterId)
    {
        try {
            log_message('info', "=== STARTING AUTO-POPULATION DEBUG ===");
            log_message('info', "Semester ID: {$semesterId}");

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
            log_message('info', "Semester found: " . json_encode($semester));

            // Get all lecturers using direct database query for debugging
            $db = \Config\Database::connect();

            // Debug: Check if lecturers table exists and has data
            $lecturersCount = $db->query("SELECT COUNT(*) as count FROM lecturers")->getRow()->count;
            log_message('info', "Total lecturers in database: {$lecturersCount}");

            if ($lecturersCount == 0) {
                log_message('warning', 'No lecturers found in database');
                return 0;
            }

            // Get lecturers using direct query to avoid any model issues
            $lecturersQuery = $db->query("SELECT id, name FROM lecturers ORDER BY name ASC");
            $lecturers = $lecturersQuery->getResultArray();

            log_message('info', "Retrieved " . count($lecturers) . " lecturers from database");
            log_message('debug', "First 3 lecturers: " . json_encode(array_slice($lecturers, 0, 3)));

            if (empty($lecturers)) {
                log_message('warning', 'No lecturers retrieved from database');
                return 0;
            }

            // Check existing commitment records for this semester
            $existingQuery = $db->query("SELECT lecturer_id FROM commitment WHERE semester_id = ?", [$semesterId]);
            $existingRecords = array_column($existingQuery->getResultArray(), 'lecturer_id');

            log_message('info', "Found " . count($existingRecords) . " existing commitment records for semester {$semesterId}");
            log_message('debug', "Existing lecturer IDs: " . json_encode($existingRecords));

            $newRecords = [];
            $addedCount = 0;

            foreach ($lecturers as $lecturer) {
                // Check if commitment record exists for this lecturer and semester
                if (!in_array($lecturer['id'], $existingRecords)) {
                    // Auto-calculate score for default values
                    $defaultScore = $this->calculateAverageScore('inactive', 0);

                    $newRecord = [
                        'lecturer_id' => (int)$lecturer['id'],
                        'semester_id' => (int)$semesterId,
                        'competence' => 'inactive',
                        'tridharma_pass' => 0,
                        'score' => $defaultScore, // Auto-calculated score
                        'updated_by' => null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $newRecords[] = $newRecord;
                    $addedCount++;

                    log_message('debug', "Prepared new record for lecturer ID: {$lecturer['id']} with auto-calculated score: {$defaultScore}");
                }
            }

            log_message('info', "Prepared {$addedCount} new records for insertion");

            // Insert records one by one for better error tracking if bulk insert fails
            if (!empty($newRecords)) {
                try {
                    // Try bulk insert first
                    log_message('info', "Attempting bulk insert of {$addedCount} records");

                    $insertResult = $this->insertBatch($newRecords);

                    if ($insertResult) {
                        log_message('info', "✓ Successfully bulk inserted {$addedCount} commitment records");
                    } else {
                        log_message('warning', "Bulk insert returned false, trying individual inserts");

                        // Fall back to individual inserts
                        $individualSuccess = 0;
                        foreach ($newRecords as $record) {
                            try {
                                $result = $this->insert($record);
                                if ($result) {
                                    $individualSuccess++;
                                    log_message('debug', "✓ Inserted record for lecturer {$record['lecturer_id']}");
                                } else {
                                    log_message('error', "✗ Failed to insert record for lecturer {$record['lecturer_id']}");
                                    log_message('error', "Validation errors: " . json_encode($this->errors()));
                                }
                            } catch (\Exception $e) {
                                log_message('error', "Exception inserting record for lecturer {$record['lecturer_id']}: " . $e->getMessage());
                            }
                        }

                        log_message('info', "Individual inserts: {$individualSuccess}/{$addedCount} successful");
                        $addedCount = $individualSuccess;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Exception during insert: ' . $e->getMessage());
                    log_message('error', 'Stack trace: ' . $e->getTraceAsString());

                    // Try individual inserts as last resort
                    log_message('info', "Bulk insert failed, attempting individual inserts as fallback");

                    $individualSuccess = 0;
                    foreach ($newRecords as $record) {
                        try {
                            // Use direct database insert to bypass model validation issues
                            $insertQuery = "INSERT INTO commitment (lecturer_id, semester_id, competence, tridharma_pass, score, updated_by, updated_at) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?)";

                            $result = $db->query($insertQuery, [
                                $record['lecturer_id'],
                                $record['semester_id'],
                                $record['competence'],
                                $record['tridharma_pass'],
                                $record['score'],
                                $record['updated_by'],
                                $record['updated_at']
                            ]);

                            if ($result) {
                                $individualSuccess++;
                                log_message('debug', "✓ Direct DB insert successful for lecturer {$record['lecturer_id']}");
                            } else {
                                log_message('error', "✗ Direct DB insert failed for lecturer {$record['lecturer_id']}");
                            }
                        } catch (\Exception $e2) {
                            log_message('error', "Exception in direct DB insert for lecturer {$record['lecturer_id']}: " . $e2->getMessage());
                        }
                    }

                    $addedCount = $individualSuccess;
                    log_message('info', "Fallback direct DB inserts: {$individualSuccess} successful");
                }
            } else {
                log_message('info', "No new commitment records needed for semester {$semesterId}");
            }

            // Verify the insertion by checking the current count
            $finalCountQuery = $db->query("SELECT COUNT(*) as count FROM commitment WHERE semester_id = ?", [$semesterId]);
            $finalCount = $finalCountQuery->getRow()->count;
            log_message('info', "Final commitment records count for semester {$semesterId}: {$finalCount}");

            log_message('info', "=== AUTO-POPULATION COMPLETED ===");
            log_message('info', "Added {$addedCount} new commitment records for semester {$semesterId}");

            return $addedCount;
        } catch (\Exception $e) {
            log_message('error', '=== AUTO-POPULATION FAILED ===');
            log_message('error', 'Error in autoPopulateCommitmentData: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Sync commitment data with lecturers table (remove orphaned records)
     */
    public function syncCommitmentData($semesterId)
    {
        try {
            $db = \Config\Database::connect();

            // Remove commitment records for lecturers that no longer exist
            $db->query("
                DELETE c FROM commitment c
                LEFT JOIN lecturers l ON c.lecturer_id = l.id
                WHERE l.id IS NULL AND c.semester_id = ?
            ", [$semesterId]);
            $deletedCount = $db->affectedRows();

            if ($deletedCount > 0) {
                log_message('info', "Removed {$deletedCount} orphaned commitment records");
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error in syncCommitmentData: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh all commitment data for a semester (enhanced version)
     */
    public function refreshCommitmentData($semesterId)
    {
        try {
            log_message('info', "Starting comprehensive commitment data refresh for semester: {$semesterId}");

            // Step 1: Sync data (remove orphaned records)
            $this->syncCommitmentData($semesterId);

            // Step 2: Auto-populate missing lecturer records
            $addedCount = $this->autoPopulateCommitmentData($semesterId);

            // Step 3: Recalculate scores for zero-score records
            $recalculatedCount = $this->recalculateZeroScores($semesterId);

            log_message('info', "Commitment data refresh completed - Added: {$addedCount}, Recalculated: {$recalculatedCount}");
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Error in refreshCommitmentData: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get commitment statistics for a specific semester
     */
    public function getCommitmentStats($semesterId)
    {
        $allLecturers = $this->getAllLecturersWithCommitment($semesterId);
        $totalLecturers = count($allLecturers);

        $activeCompetence = 0;
        $passTriDharma = 0;

        foreach ($allLecturers as $lecturer) {
            if ($lecturer['competence'] === 'active') {
                $activeCompetence++;
            }
            if ($lecturer['tridharma_pass'] == 1) {
                $passTriDharma++;
            }
        }

        return [
            'total_lecturers' => $totalLecturers,
            'active_competence' => $activeCompetence,
            'inactive_competence' => $totalLecturers - $activeCompetence,
            'pass_tri_dharma' => $passTriDharma,
            'fail_tri_dharma' => $totalLecturers - $passTriDharma,
            'active_competence_percentage' => $totalLecturers > 0 ? round(($activeCompetence / $totalLecturers) * 100, 1) : 0,
            'pass_tri_dharma_percentage' => $totalLecturers > 0 ? round(($passTriDharma / $totalLecturers) * 100, 1) : 0
        ];
    }

    /**
     * Recalculate ALL scores for semester (similar to DisciplineModel)
     */
    public function recalculateAllScores($semesterId)
    {
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
                $newScore = $this->calculateCommitmentScore(
                    $record['competence'],
                    $record['tridharma_pass']
                );

                // Only update if score has changed
                if ((int)$record['score'] !== $newScore) {
                    $this->update($record['id'], [
                        'score' => $newScore,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $updatedCount++;
                    log_message('debug', "Updated commitment score for lecturer {$record['lecturer_id']}: {$record['score']} -> {$newScore}");
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            log_message('info', "Recalculated {$updatedCount} commitment scores for semester {$semesterId}");
            return $updatedCount;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in recalculateAllScores: ' . $e->getMessage());
            throw $e;
        }
    }
}
