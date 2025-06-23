<?php

namespace App\Controllers;

use App\Models\OrientationDataUploadModel;
use App\Models\LecturerModel;
use App\Models\ServiceOrientationModel;
use App\Models\SemesterModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\ResponseInterface;

class OrientationDataUploadController extends BaseController
{
    protected $dataUploadModel;
    protected $lecturerModel;
    protected $serviceOrientationModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->dataUploadModel = new OrientationDataUploadModel();
        $this->lecturerModel = new LecturerModel();
        $this->serviceOrientationModel = new ServiceOrientationModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Upload and process Service Orientation data from XLSX/CSV file
     */
    public function uploadPelayanan()
    {
        if (!$this->request->isAJAX() && !$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $file = $this->request->getFile('file_pelayanan');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan');
        }

        // Validate file type
        $allowedTypes = ['xlsx', 'xls', 'csv'];
        $extension = $file->getClientExtension();

        if (!in_array($extension, $allowedTypes)) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan XLSX, XLS, atau CSV');
        }

        try {
            // Process the uploaded file
            $result = $this->processOrientationFile($file);

            if ($result['success']) {
                $message = "Upload berhasil! {$result['processed']} data diproses";

                // Recalculate all scores after upload using ServiceOrientationModel
                $currentSemester = $this->semesterModel->getCurrentSemester();
                if ($currentSemester) {
                    $this->serviceOrientationModel->recalculateAllScores($currentSemester['id']);
                }

                if (!empty($result['errors'])) {
                    return redirect()->back()
                        ->with('success', $message)
                        ->with('errors', $result['errors']);
                } else {
                    return redirect()->back()->with('success', $message);
                }
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->with('errors', $result['errors'] ?? []);
            }
        } catch (\Exception $e) {
            log_message('error', 'Upload Pelayanan Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process Service Orientation file and auto-fill data based on existing database records
     */
    private function processOrientationFile($file)
    {
        $spreadsheet = IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Expected columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, NILAI ANGKET
        $expectedHeaders = ['NO', 'NAMA DOSEN', 'NIP/NPT/NPPPK', 'ASAL PRODI', 'NILAI ANGKET'];

        if (empty($data) || count($data) < 2) {
            return ['success' => false, 'message' => 'File kosong atau tidak memiliki data'];
        }

        // Verify headers
        $headers = array_map('trim', $data[0]);
        $headerValid = $this->validateHeaders($headers, $expectedHeaders);

        if (!$headerValid) {
            return [
                'success' => false,
                'message' => 'Format header tidak sesuai. Expected: ' . implode(', ', $expectedHeaders)
            ];
        }

        // Get current semester
        $currentSemester = $this->semesterModel->getCurrentSemester();
        if (!$currentSemester) {
            return ['success' => false, 'message' => 'Semester aktif tidak ditemukan'];
        }

        $processed = 0;
        $errors = [];

        // Process each row (skip header)
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $result = $this->processOrientationRow($row, $currentSemester['id'], $i + 1);
                if ($result['success']) {
                    $processed++;
                } else {
                    $errors[] = "Baris " . ($i + 1) . ": " . $result['error'];
                }
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                log_message('error', "Error processing orientation row {$i}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ];
    }

    /**
     * Process a single service orientation row with auto-fill capability
     */
    private function processOrientationRow($row, $semesterId, $rowNumber)
    {
        // Map columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, NILAI ANGKET
        $no = trim($row[0] ?? '');
        $namaDosen = trim($row[1] ?? '');
        $nip = preg_replace('/\s+/', '', trim($row[2] ?? ''));
        $asalProdi = trim($row[3] ?? '');
        $nilaiAngket = $row[4] ?? null;

        if (empty($nip)) {
            return ['success' => false, 'error' => 'NIP tidak boleh kosong'];
        }

        // Find lecturer by NIP
        $lecturer = $this->lecturerModel->where('nip', $nip)->first();
        if (!$lecturer) {
            return ['success' => false, 'error' => "Dosen dengan NIP {$nip} tidak ditemukan di database"];
        }

        // Auto-fill from existing service orientation data if values are empty
        $existingOrientation = $this->serviceOrientationModel
            ->where('lecturer_id', $lecturer['id'])
            ->where('semester_id', $semesterId)
            ->first();

        if ($existingOrientation) {
            // Fill missing values from existing data
            if (empty($nilaiAngket) || $nilaiAngket === null) {
                $nilaiAngket = $existingOrientation['questionnaire_score'];
            }
        }

        // Validate numeric values
        if (!is_numeric($nilaiAngket) || $nilaiAngket < 0 || $nilaiAngket > 100) {
            return ['success' => false, 'error' => 'Nilai angket harus berupa angka 0-100'];
        }

        // Prepare data for insertion/update using ServiceOrientationModel
        $orientationData = [
            'questionnaire_score' => (float)$nilaiAngket
        ];

        // Use ServiceOrientationModel's updateLecturerServiceOrientation method (handles score calculation)
        $result = $this->serviceOrientationModel->updateLecturerServiceOrientation(
            $lecturer['id'],
            $semesterId,
            $orientationData,
            session()->get('user_id')
        );

        return ['success' => $result, 'error' => $result ? null : 'Gagal menyimpan data ke database'];
    }

    /**
     * Validate headers against expected headers
     */
    private function validateHeaders($actualHeaders, $expectedHeaders)
    {
        if (count($actualHeaders) < count($expectedHeaders)) {
            return false;
        }

        for ($i = 0; $i < count($expectedHeaders); $i++) {
            if (!isset($actualHeaders[$i]) || trim($actualHeaders[$i]) !== trim($expectedHeaders[$i])) {
                return false;
            }
        }

        return true;
    }
}
