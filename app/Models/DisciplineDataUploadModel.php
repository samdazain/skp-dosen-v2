<?php

namespace App\Models;

use CodeIgniter\Model;

class DisciplineDataUploadModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'discipline'; // Default table for discipline
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
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
     * Insert or update discipline data
     */
    public function insertOrUpdateDiscipline($data)
    {
        $db = \Config\Database::connect();

        // Check if record exists
        $existing = $db->table('discipline')
            ->where('lecturer_id', $data['lecturer_id'])
            ->where('semester_id', $data['semester_id'])
            ->get()
            ->getRowArray();

        if ($existing) {
            // Update existing record
            return $db->table('discipline')
                ->where('lecturer_id', $data['lecturer_id'])
                ->where('semester_id', $data['semester_id'])
                ->update($data);
        } else {
            // Insert new record
            $data['created_at'] = date('Y-m-d H:i:s');
            return $db->table('discipline')->insert($data);
        }
    }

    /**
     * Get existing discipline data by lecturer and semester
     */
    public function getDisciplineData($lecturerId, $semesterId)
    {
        $db = \Config\Database::connect();

        return $db->table('discipline')
            ->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->get()
            ->getRowArray();
    }

    /**
     * Bulk update discipline data from uploaded file
     */
    public function bulkUpdateDiscipline($disciplineDataArray, $semesterId, $updatedBy = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $updatedCount = 0;
        $errorCount = 0;

        foreach ($disciplineDataArray as $data) {
            try {
                $disciplineData = [
                    'lecturer_id' => $data['lecturer_id'],
                    'semester_id' => $semesterId,
                    'daily_absence' => (int)$data['daily_absence'],
                    'exercise_morning_absence' => (int)$data['exercise_morning_absence'],
                    'ceremony_absence' => (int)$data['ceremony_absence'],
                    'updated_by' => $updatedBy,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $result = $this->insertOrUpdateDiscipline($disciplineData);
                if ($result) {
                    $updatedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                log_message('error', 'Error updating discipline data: ' . $e->getMessage());
            }
        }

        $db->transComplete();

        return [
            'success' => $db->transStatus(),
            'updated_count' => $updatedCount,
            'error_count' => $errorCount
        ];
    }

    /**
     * Validate discipline data format
     */
    public function validateDisciplineData($data)
    {
        $errors = [];

        // Validate daily absence
        if (!isset($data['daily_absence']) || !is_numeric($data['daily_absence'])) {
            $errors[] = 'Absen harian harus berupa angka';
        } elseif ($data['daily_absence'] < 0) {
            $errors[] = 'Absen harian harus positif atau nol';
        }

        // Validate exercise morning absence
        if (!isset($data['exercise_morning_absence']) || !is_numeric($data['exercise_morning_absence'])) {
            $errors[] = 'Absen senam pagi harus berupa angka';
        } elseif ($data['exercise_morning_absence'] < 0) {
            $errors[] = 'Absen senam pagi harus positif atau nol';
        }

        // Validate ceremony absence
        if (!isset($data['ceremony_absence']) || !is_numeric($data['ceremony_absence'])) {
            $errors[] = 'Absen upacara harus berupa angka';
        } elseif ($data['ceremony_absence'] < 0) {
            $errors[] = 'Absen upacara harus positif atau nol';
        }

        return $errors;
    }
}
