<?php
// filepath: d:\KULIAH\SEMESTER6\PKL\Project\skp-dosen\app\Models\SemesterModel.php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table = 'semesters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['year', 'term', 'updated_by', 'updated_at'];
    protected $useTimestamps = true;

    /**
     * Get all available semesters
     * 
     * @return array
     */
    public function getAllSemesters()
    {
        return $this->orderBy('year', 'DESC')
            ->orderBy('term', 'DESC')
            ->findAll();
    }

    /**
     * Get current active semester
     * If no semester is set as active, return the latest semester
     * 
     * @return array|null
     */
    public function getCurrentSemester()
    {
        $activeSemesterId = session()->get('activeSemesterId');

        // If there's an active semester ID in session, validate it still exists
        if ($activeSemesterId) {
            $semester = $this->find($activeSemesterId);
            if ($semester) {
                return $semester;
            } else {
                // If semester no longer exists, clear the session
                session()->remove('activeSemesterId');
                session()->remove('activeSemesterText');
            }
        }

        // Get the default semester based on current date
        $defaultSemester = $this->getDefaultSemester();

        // Auto-set as active if found
        if ($defaultSemester) {
            session()->set('activeSemesterId', $defaultSemester['id']);
            session()->set('activeSemesterText', $this->formatSemester($defaultSemester));
        }

        return $defaultSemester;
    }

    /**
     * Alias for getCurrentSemester()
     */
    public function getActiveSemester()
    {
        return $this->getCurrentSemester();
    }

    /**
     * Get the default semester based on the current date
     * 
     * @return array|null
     */
    public function getDefaultSemester()
    {
        $currentYear = date('Y');
        $currentMonth = (int)date('n'); // Get month as integer (1-12)

        // Determine default semester based on current date
        // If month >= 7 (July or later), set semester to 2 (Genap)
        // If month >= 2 (February or later), set semester to 1 (Ganjil)
        // January defaults to previous year's semester 2

        if ($currentMonth >= 7) {
            // July to December = Genap of current year
            $defaultTerm = '2';
            $defaultYear = $currentYear;
        } elseif ($currentMonth >= 2) {
            // February to June = Ganjil of current year
            $defaultTerm = '1';
            $defaultYear = $currentYear;
        } else {
            // January = Genap of previous year
            $defaultTerm = '2';
            $defaultYear = $currentYear - 1;
        }

        // First, try to find the exact semester based on date logic
        $semester = $this->where('year', $defaultYear)
            ->where('term', $defaultTerm)
            ->first();

        // If exact semester not found, get the most recent semester with ID >= 7 if specified
        if (!$semester) {
            $semester = $this->where('id >=', 7)
                ->orderBy('year', 'DESC')
                ->orderBy('term', 'DESC')
                ->first();
        }

        // If still no semester found, get the most recent one available
        if (!$semester) {
            $semester = $this->orderBy('year', 'DESC')
                ->orderBy('term', 'DESC')
                ->first();
        }

        // Log the selection for debugging
        if (ENVIRONMENT === 'development') {
            log_message('debug', 'Default semester selection - Current month: ' . $currentMonth);
            log_message('debug', 'Default semester selection - Selected year: ' . $defaultYear . ', term: ' . $defaultTerm);
            log_message('debug', 'Default semester selection - Found semester: ' . json_encode($semester));
        }

        return $semester;
    }

    /**
     * Get semester for specific date conditions
     * 
     * @param int|null $year
     * @param int|null $month
     * @return array|null
     */
    public function getSemesterByDate($year = null, $month = null)
    {
        $year = $year ?: date('Y');
        $month = $month ?: (int)date('n');

        // Apply the same logic as getDefaultSemester but for specific date
        if ($month >= 7) {
            $term = '2';
            $semesterYear = $year;
        } elseif ($month >= 2) {
            $term = '1';
            $semesterYear = $year;
        } else {
            $term = '2';
            $semesterYear = $year - 1;
        }

        $semester = $this->where('year', $semesterYear)
            ->where('term', $term)
            ->first();

        // If not found and ID >= 7 condition is needed
        if (!$semester) {
            $semester = $this->where('id >=', 7)
                ->orderBy('year', 'DESC')
                ->orderBy('term', 'DESC')
                ->first();
        }

        return $semester;
    }

    /**
     * Format semester for display
     * 
     * @param array $semester
     * @return string
     */
    public function formatSemester($semester)
    {
        if (empty($semester)) {
            return 'Semester Tidak Ditemukan';
        }

        $termText = ($semester['term'] == '1') ? 'Ganjil' : 'Genap';
        $yearRange = $semester['year'] . '/' . ($semester['year'] + 1);

        return "Semester $termText $yearRange";
    }

    /**
     * Get semester by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getSemesterById($id)
    {
        $result = $this->find($id);

        // Handle both array and object results
        if (is_object($result)) {
            return method_exists($result, 'toArray') ? $result->toArray() : (array)$result;
        }

        return is_array($result) ? $result : null;
    }

    /**
     * Check if a semester exists
     * 
     * @param int $year
     * @param int $term
     * @param int|null $excludeId
     * @return bool
     */
    public function semesterExists($year, $term, $excludeId = null)
    {
        $builder = $this->where('year', $year)->where('term', $term);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Check if a semester is the active semester
     * 
     * @param int $id
     * @return bool
     */
    public function isActiveSemester($id)
    {
        return session()->get('activeSemesterId') == $id;
    }

    /**
     * Get term options
     * 
     * @return array
     */
    public function getTermOptions()
    {
        return [
            '1' => 'Ganjil',
            '2' => 'Genap'
        ];
    }

    /**
     * Get a range of years for selection
     * 
     * @param int|null $startYear
     * @param int|null $endYear
     * @return array
     */
    public function getYearRange($startYear = null, $endYear = null)
    {
        if (!$startYear) {
            $startYear = date('Y') - 5;
        }
        if (!$endYear) {
            $endYear = date('Y') + 2;
        }

        $years = [];
        for ($year = $endYear; $year >= $startYear; $year--) {
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Get semester statistics
     */
    public function getSemesterStats()
    {
        $total = $this->countAll();
        $currentYear = date('Y');

        $currentYearCount = $this->where('year', $currentYear)->countAllResults();

        return [
            'total' => $total,
            'current_year' => $currentYearCount,
            'latest' => $this->orderBy('year', 'DESC')->orderBy('term', 'DESC')->first()
        ];
    }
}
