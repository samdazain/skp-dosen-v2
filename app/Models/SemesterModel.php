<?php
// filepath: d:\KULIAH\SEMESTER6\PKL\Project\skp-dosen\app\Models\SemesterModel.php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table = 'semesters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['year', 'term', 'created_at', 'updated_at'];
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

        if ($activeSemesterId) {
            $semester = $this->find($activeSemesterId);
            if ($semester) {
                return $semester;
            }
        }

        return $this->getDefaultSemester();
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
        $currentMonth = date('n');

        // Determine default semester based on current date
        if ($currentMonth >= 8) {
            // August to December = Ganjil of current year
            $defaultTerm = '1';
            $defaultYear = $currentYear;
        } else {
            // January to July
            if ($currentMonth >= 2) {
                // February to July = Genap of previous year
                $defaultTerm = '2';
                $defaultYear = $currentYear - 1;
            } else {
                // January = Ganjil of previous year
                $defaultTerm = '1';
                $defaultYear = $currentYear - 1;
            }
        }

        $semester = $this->where('year', $defaultYear)
            ->where('term', $defaultTerm)
            ->first();

        if (!$semester) {
            // If specific semester not found, get the most recent one
            $semester = $this->orderBy('year', 'DESC')
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
