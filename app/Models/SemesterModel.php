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
        // For a real app, you might have an 'is_active' field
        // Here we'll just return the most recent semester
        return $this->orderBy('year', 'DESC')
            ->orderBy('term', 'DESC')
            ->first();
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
        // Get the result
        $result = $this->find($id);

        // Ensure we return array|null as specified in docblock
        if (is_object($result)) {
            if (method_exists($result, 'first')) {
                $result = $result->first();
            }

            if ($result !== null) {
                if (method_exists($result, 'toArray')) {
                    return $result->toArray();
                } else {
                    return (array)$result;
                }
            }
        }

        return $result;
    }
    /**
     * Provide dummy data for development
     * 
     * @return array
     */
    public function getDummySemesters()
    {
        $currentYear = date('Y');

        return [
            [
                'id' => 1,
                'year' => $currentYear - 2,
                'term' => '1',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 years')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 years'))
            ],
            [
                'id' => 2,
                'year' => $currentYear - 2,
                'term' => '2',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1.5 years')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1.5 years'))
            ],
            [
                'id' => 3,
                'year' => $currentYear - 1,
                'term' => '1',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 year')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 year'))
            ],
            [
                'id' => 4,
                'year' => $currentYear - 1,
                'term' => '2',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 months')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-6 months'))
            ],
            [
                'id' => 5,
                'year' => $currentYear,
                'term' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * Override findAll to use dummy data during development
     */
    public function findAll(int|null $limit = null, int $offset = 0)
    {
        if (ENVIRONMENT === 'production') {
            return parent::findAll($limit, $offset);
        }

        $data = $this->getDummySemesters();

        if ($limit > 0) {
            $data = array_slice($data, $offset, $limit);
        } elseif ($offset > 0) {
            $data = array_slice($data, $offset);
        }

        return $data;
    }

    /**
     * Override first to use dummy data during development
     */
    public function first()
    {
        if (ENVIRONMENT === 'production') {
            return parent::first();
        }

        $data = $this->getDummySemesters();
        return reset($data); // Return first item
    }

    /**
     * Override find to use dummy data during development
     */
    public function find($id = null)
    {
        if (ENVIRONMENT === 'production') {
            return parent::find($id);
        }

        if ($id === null) {
            return $this->findAll();
        }

        foreach ($this->getDummySemesters() as $semester) {
            if ($semester['id'] == $id) {
                return $semester;
            }
        }

        return null;
    }
}
