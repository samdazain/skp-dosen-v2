<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceOrientationModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service_orientation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lecturer_id',
        'semester_id',
        'questionnaire_score',
        'score',
        'updated_by',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'lecturer_id' => 'required|is_natural_no_zero',
        'semester_id' => 'required|is_natural_no_zero',
        'questionnaire_score' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]'
    ];
    protected $validationMessages   = [
        'lecturer_id' => [
            'required' => 'Lecturer ID is required',
            'is_natural_no_zero' => 'Lecturer ID must be a valid number'
        ],
        'semester_id' => [
            'required' => 'Semester ID is required',
            'is_natural_no_zero' => 'Semester ID must be a valid number'
        ],
        'questionnaire_score' => [
            'required' => 'Questionnaire score is required',
            'decimal' => 'Questionnaire score must be a valid number',
            'greater_than_equal_to' => 'Questionnaire score must be at least 0',
            'less_than_equal_to' => 'Questionnaire score cannot exceed 100'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
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
     * Auto-populate service orientation table with all lecturers for given semester
     */
    public function autoPopulateServiceOrientationData($semesterId)
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
            // Check if service orientation record exists for this lecturer and semester
            if (!in_array($lecturer['id'], $existingRecords)) {
                $newRecords[] = [
                    'lecturer_id' => $lecturer['id'],
                    'semester_id' => $semesterId,
                    'questionnaire_score' => 0.0, // Default: no questionnaire score recorded
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
                log_message('info', "Auto-populated {$addedCount} service orientation records for semester {$semesterId}");
            } catch (\Exception $e) {
                log_message('error', 'Error auto-populating service orientation data: ' . $e->getMessage());
                throw $e;
            }
        }

        return $addedCount;
    }

    /**
     * Calculate service orientation score based on questionnaire score
     */
    public function calculateServiceOrientationScore($questionnaireScore)
    {
        // Get score from score settings
        $score = $this->scoreModel->calculateScore('orientation', 'teaching_questionnaire', (float)$questionnaireScore);

        return (int)round($score);
    }

    /**
     * Update service orientation data and recalculate score
     */
    public function updateLecturerServiceOrientation($lecturerId, $semesterId, $data, $updatedBy = null)
    {
        // Calculate score
        $score = $this->calculateServiceOrientationScore($data['questionnaire_score']);

        $serviceOrientationData = [
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId,
            'questionnaire_score' => (float)$data['questionnaire_score'],
            'score' => $score,
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if record exists
        $existing = $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $serviceOrientationData);
        } else {
            // Create if somehow missing
            return $this->insert($serviceOrientationData);
        }
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
                $newScore = $this->calculateServiceOrientationScore($record['questionnaire_score']);

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
     * Get service orientation data with lecturer information for current semester
     * Auto-populate missing lecturers and include position ordering
     */
    public function getServiceOrientationDataWithLecturers($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [];
        }

        // Auto-populate service orientation table with all lecturers for this semester
        $this->autoPopulateServiceOrientationData($semesterId);

        // Get the data with lecturer information, ordered by position hierarchy
        $data = $this->select('
                service_orientation.*,
                lecturers.nip,
                lecturers.name as lecturer_name,
                lecturers.position,
                lecturers.study_program
            ')
            ->join('lecturers', 'lecturers.id = service_orientation.lecturer_id')
            ->where('service_orientation.semester_id', $semesterId)
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
     * Get service orientation data with lecturer information for current semester
     */
    public function getServiceOrientationWithLecturer($semesterId = null)
    {
        $builder = $this->select('
            service_orientation.*,
            lecturers.name as lecturer_name,
            lecturers.nip as lecturer_nip,
            lecturers.study_program,
            semesters.year,
            semesters.term
        ')
            ->join('lecturers', 'lecturers.id = service_orientation.lecturer_id')
            ->join('semesters', 'semesters.id = service_orientation.semester_id');

        if ($semesterId) {
            $builder->where('service_orientation.semester_id', $semesterId);
        }

        return $builder->findAll();
    }

    /**
     * Get service orientation data by lecturer and semester
     */
    public function getByLecturerAndSemester($lecturerId, $semesterId)
    {
        return $this->where([
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId
        ])->first();
    }

    /**
     * Update or insert service orientation record
     */
    public function updateOrInsert($data)
    {
        $existing = $this->getByLecturerAndSemester($data['lecturer_id'], $data['semester_id']);

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->insert($data);
        }
    }

    /**
     * Get statistics for service orientation scores
     */
    public function getScoreStatistics($semesterId = null)
    {
        $builder = $this->select('
            COUNT(*) as total_records,
            AVG(questionnaire_score) as avg_questionnaire_score,
            MIN(questionnaire_score) as min_questionnaire_score,
            MAX(questionnaire_score) as max_questionnaire_score,
            AVG(score) as avg_score,
            MIN(score) as min_score,
            MAX(score) as max_score
        ');

        if ($semesterId) {
            $builder->where('semester_id', $semesterId);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Get service orientation data by study program
     */
    public function getByStudyProgram($studyProgram, $semesterId = null)
    {
        $builder = $this->select('
            service_orientation.*,
            lecturers.name as lecturer_name,
            lecturers.nip as lecturer_nip
        ')
            ->join('lecturers', 'lecturers.id = service_orientation.lecturer_id')
            ->where('lecturers.study_program', $studyProgram);

        if ($semesterId) {
            $builder->where('service_orientation.semester_id', $semesterId);
        }

        return $builder->findAll();
    }

    /**
     * Delete service orientation records by semester
     */
    public function deleteBySemester($semesterId)
    {
        return $this->where('semester_id', $semesterId)->delete();
    }

    /**
     * Get service orientation summary by semester
     */
    public function getSemesterSummary($semesterId)
    {
        return $this->select('
            COUNT(*) as total_lecturers,
            AVG(questionnaire_score) as avg_questionnaire_score,
            AVG(score) as avg_score,
            COUNT(CASE WHEN score >= 80 THEN 1 END) as excellent_count,
            COUNT(CASE WHEN score >= 60 AND score < 80 THEN 1 END) as good_count,
            COUNT(CASE WHEN score < 60 THEN 1 END) as needs_improvement_count
        ')
            ->where('semester_id', $semesterId)
            ->get()
            ->getRowArray();
    }
}
