<?php

namespace App\Models;

use CodeIgniter\Model;

class IntegrityDataUploadModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'integrity'; // Default table, will vary
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
     * Insert or update integrity data
     */
    public function insertOrUpdateIntegrity($data)
    {
        $db = \Config\Database::connect();

        // Check if record exists
        $existing = $db->table('integrity')
            ->where('lecturer_id', $data['lecturer_id'])
            ->where('semester_id', $data['semester_id'])
            ->get()
            ->getRowArray();

        if ($existing) {
            // Update existing record
            return $db->table('integrity')
                ->where('lecturer_id', $data['lecturer_id'])
                ->where('semester_id', $data['semester_id'])
                ->update($data);
        } else {
            // Insert new record
            $data['created_at'] = date('Y-m-d H:i:s');
            return $db->table('integrity')->insert($data);
        }
    }

    /**
     * Get existing integrity data by lecturer and semester
     */
    public function getIntegrityData($lecturerId, $semesterId)
    {
        $db = \Config\Database::connect();

        return $db->table('integrity')
            ->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->get()
            ->getRowArray();
    }

    /**
     * Bulk update integrity data from uploaded file
     */
    public function bulkUpdateIntegrity($integrityDataArray, $semesterId, $updatedBy = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $updatedCount = 0;
        $errorCount = 0;

        foreach ($integrityDataArray as $data) {
            try {
                $integrityData = [
                    'lecturer_id' => $data['lecturer_id'],
                    'semester_id' => $semesterId,
                    'teaching_attendance' => (int)$data['teaching_attendance'],
                    'courses_taught' => (int)$data['courses_taught'],
                    'updated_by' => $updatedBy,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $result = $this->insertOrUpdateIntegrity($integrityData);
                if ($result) {
                    $updatedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                log_message('error', 'Error updating integrity data: ' . $e->getMessage());
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
     * Validate integrity data format
     */
    public function validateIntegrityData($data)
    {
        $errors = [];

        // Validate teaching attendance
        if (!isset($data['teaching_attendance']) || !is_numeric($data['teaching_attendance'])) {
            $errors[] = 'Kehadiran mengajar harus berupa angka';
        } elseif ($data['teaching_attendance'] < 0 || $data['teaching_attendance'] > 100) {
            $errors[] = 'Kehadiran mengajar harus antara 0-100';
        }

        // Validate courses taught
        if (!isset($data['courses_taught']) || !is_numeric($data['courses_taught'])) {
            $errors[] = 'Jumlah mata kuliah harus berupa angka';
        } elseif ($data['courses_taught'] < 0) {
            $errors[] = 'Jumlah mata kuliah harus positif';
        }

        return $errors;
    }
}
