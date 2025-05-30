<?php

namespace App\Models;

use CodeIgniter\Model;

class IntegrityModel extends Model
{
    protected $table = 'integrity';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'lecturer_id',
        'semester_id',
        'teaching_attendance',
        'courses_taught',
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
            $newScore = $this->calculateIntegrityScore(
                $record['teaching_attendance'],
                $record['courses_taught']
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

    public function recalculateAllScores($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return false;
        }

        // Get all records for the semester
        $allRecords = $this->where('semester_id', $semesterId)->findAll();

        $updatedCount = 0;

        foreach ($allRecords as $record) {
            // Calculate new score
            $newScore = $this->calculateIntegrityScore(
                $record['teaching_attendance'],
                $record['courses_taught']
            );

            // Update the score
            $this->update($record['id'], [
                'score' => $newScore,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Get integrity data with lecturer information for current semester
     * Auto-populate missing lecturers and include position ordering
     */
    public function getIntegrityDataWithLecturers($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [];
        }

        // Auto-populate integrity table with all lecturers for this semester
        $this->autoPopulateIntegrityData($semesterId);

        // Auto-recalculate scores that are 0
        $this->recalculateZeroScores($semesterId);

        // Get the data with lecturer information, ordered by position hierarchy
        $data = $this->select('
                integrity.*,
                lecturers.nip,
                lecturers.name as lecturer_name,
                lecturers.position,
                lecturers.study_program
            ')
            ->join('lecturers', 'lecturers.id = integrity.lecturer_id')
            ->where('integrity.semester_id', $semesterId)
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
     * Auto-populate integrity table with all lecturers for given semester
     */
    public function autoPopulateIntegrityData($semesterId)
    {
        // Get all lecturers
        $lecturers = $this->lecturerModel->findAll();

        foreach ($lecturers as $lecturer) {
            // Check if integrity record exists for this lecturer and semester
            $existing = $this->where('lecturer_id', $lecturer['id'])
                ->where('semester_id', $semesterId)
                ->first();

            // If not exists, create with default values
            if (!$existing) {
                $defaultData = [
                    'lecturer_id' => $lecturer['id'],
                    'semester_id' => $semesterId,
                    'teaching_attendance' => 0, // Default: no absences
                    'courses_taught' => 0, // Default: no courses
                    'score' => 0, // Will be calculated when real data is entered
                    'updated_by' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->insert($defaultData);
            }
        }
    }

    /**
     * Calculate integrity score based on teaching attendance and courses taught
     */
    public function calculateIntegrityScore($teachingAttendance, $coursesTaught)
    {
        // Get scores from score settings
        $attendanceScore = $this->scoreModel->calculateScore('integrity', 'teaching_attendance', (int)$teachingAttendance);
        $coursesScore = $this->scoreModel->calculateScore('integrity', 'courses_taught', (int)$coursesTaught);

        // Calculate average score (attendance score + courses score) / 2
        $totalScore = ($attendanceScore + $coursesScore) / 2;

        return (int)round($totalScore);
    }

    /**
     * Update integrity data and recalculate score
     */
    public function updateLecturerIntegrity($lecturerId, $semesterId, $data, $updatedBy = null)
    {
        // Calculate score
        $score = $this->calculateIntegrityScore($data['teaching_attendance'], $data['courses_taught']);

        $integrityData = [
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId,
            'teaching_attendance' => (int)$data['teaching_attendance'],
            'courses_taught' => (int)$data['courses_taught'],
            'score' => $score,
            'updated_by' => $updatedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if record exists (should exist due to auto-population)
        $existing = $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $integrityData);
        } else {
            // Create if somehow missing
            return $this->insert($integrityData);
        }
    }

    /**
     * Get integrity statistics for current semester
     */
    public function getIntegrityStatistics($semesterId = null)
    {
        if (!$semesterId) {
            $semesterModel = new SemesterModel();
            $currentSemester = $semesterModel->getCurrentSemester();
            $semesterId = $currentSemester ? $currentSemester['id'] : null;
        }

        if (!$semesterId) {
            return [
                'average_attendance_score' => 0,
                'average_courses_score' => 0,
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
                'average_attendance_score' => 0,
                'average_courses_score' => 0,
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
        $totalAttendanceScore = 0;
        $totalCoursesScore = 0;
        $scoreDistribution = [
            'excellent' => 0, // 85-100
            'good' => 0,      // 75-84
            'fair' => 0,      // 60-74
            'poor' => 0       // <60
        ];

        foreach ($data as $record) {
            // Calculate individual component scores
            $attendanceScore = $this->scoreModel->calculateScore('integrity', 'teaching_attendance', $record['teaching_attendance']);
            $coursesScore = $this->scoreModel->calculateScore('integrity', 'courses_taught', $record['courses_taught']);

            $totalAttendanceScore += $attendanceScore;
            $totalCoursesScore += $coursesScore;

            // Distribute overall scores
            $overallScore = (int)$record['score'];
            if ($overallScore >= 85) {
                $scoreDistribution['excellent']++;
            } elseif ($overallScore >= 75) {
                $scoreDistribution['good']++;
            } elseif ($overallScore >= 60) {
                $scoreDistribution['fair']++;
            } else {
                $scoreDistribution['poor']++;
            }
        }

        return [
            'average_attendance_score' => $totalLecturers > 0 ? round($totalAttendanceScore / $totalLecturers, 1) : 0,
            'average_courses_score' => $totalLecturers > 0 ? round($totalCoursesScore / $totalLecturers, 1) : 0,
            'total_lecturers' => $totalLecturers,
            'score_distribution' => $scoreDistribution
        ];
    }

    /**
     * Bulk update integrity data from uploaded file
     */
    public function bulkUpdateIntegrity($integrityData, $semesterId, $updatedBy = null)
    {
        $this->db->transStart();

        // First, ensure all lecturers are populated
        $this->autoPopulateIntegrityData($semesterId);

        foreach ($integrityData as $data) {
            // Find lecturer by NIP
            $lecturer = $this->lecturerModel->where('nip', $data['nip'])->first();

            if ($lecturer) {
                $this->updateLecturerIntegrity(
                    $lecturer['id'],
                    $semesterId,
                    [
                        'teaching_attendance' => $data['teaching_attendance'],
                        'courses_taught' => $data['courses_taught']
                    ],
                    $updatedBy
                );
            }
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Get integrity data for specific lecturer and semester
     */
    public function getLecturerIntegrity($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->first();
    }

    /**
     * Check if lecturer has integrity data for semester
     */
    public function hasIntegrityData($lecturerId, $semesterId)
    {
        return $this->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->countAllResults() > 0;
    }

    /**
     * Get position hierarchy
     */
    public function getPositionHierarchy()
    {
        return $this->positions;
    }
}
