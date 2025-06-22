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

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get service orientation data with lecturer information
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
