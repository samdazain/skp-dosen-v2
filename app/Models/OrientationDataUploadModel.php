<?php

namespace App\Models;

use CodeIgniter\Model;

class OrientationDataUploadModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service_orientation'; // Default table for service orientation
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
     * Insert or update service orientation data
     */
    public function insertOrUpdateOrientation($data)
    {
        $db = \Config\Database::connect();

        // Check if record exists
        $existing = $db->table('service_orientation')
            ->where('lecturer_id', $data['lecturer_id'])
            ->where('semester_id', $data['semester_id'])
            ->get()
            ->getRowArray();

        if ($existing) {
            // Update existing record
            return $db->table('service_orientation')
                ->where('lecturer_id', $data['lecturer_id'])
                ->where('semester_id', $data['semester_id'])
                ->update($data);
        } else {
            // Insert new record
            $data['created_at'] = date('Y-m-d H:i:s');
            return $db->table('service_orientation')->insert($data);
        }
    }

    /**
     * Get existing service orientation data by lecturer and semester
     */
    public function getOrientationData($lecturerId, $semesterId)
    {
        $db = \Config\Database::connect();

        return $db->table('service_orientation')
            ->where('lecturer_id', $lecturerId)
            ->where('semester_id', $semesterId)
            ->get()
            ->getRowArray();
    }

    /**
     * Bulk update service orientation data from uploaded file
     */
    public function bulkUpdateOrientation($orientationDataArray, $semesterId, $updatedBy = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $updatedCount = 0;
        $errorCount = 0;

        foreach ($orientationDataArray as $data) {
            try {
                $orientationData = [
                    'lecturer_id' => $data['lecturer_id'],
                    'semester_id' => $semesterId,
                    'questionnaire_score' => (float)$data['questionnaire_score'],
                    'updated_by' => $updatedBy,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $result = $this->insertOrUpdateOrientation($orientationData);
                if ($result) {
                    $updatedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                log_message('error', 'Error updating service orientation data: ' . $e->getMessage());
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
     * Validate service orientation data format
     */
    public function validateOrientationData($data)
    {
        $errors = [];

        // Validate questionnaire score
        if (!isset($data['questionnaire_score']) || !is_numeric($data['questionnaire_score'])) {
            $errors[] = 'Nilai angket harus berupa angka';
        } elseif ($data['questionnaire_score'] < 0 || $data['questionnaire_score'] > 100) {
            $errors[] = 'Nilai angket harus antara 0-100';
        }

        return $errors;
    }
}
