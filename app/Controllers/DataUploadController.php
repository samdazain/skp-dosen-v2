<?php

namespace App\Controllers;

use App\Models\DataUploadModel;
use App\Models\LecturerModel;
use App\Models\IntegrityModel;
use App\Models\SemesterModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\ResponseInterface;

class DataUploadController extends BaseController
{
    protected $dataUploadModel;
    protected $lecturerModel;
    protected $integrityModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->dataUploadModel = new DataUploadModel();
        $this->lecturerModel = new LecturerModel();
        $this->integrityModel = new IntegrityModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Upload and process Integrity data from XLSX/CSV file
     */
    public function uploadIntegritas()
    {
        if (!$this->request->isAJAX() && !$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $file = $this->request->getFile('file_integritas');

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
            $result = $this->processIntegrityFile($file);

            if ($result['success']) {
                $message = "Upload berhasil! {$result['processed']} data diproses";

                // Recalculate all scores after upload using IntegrityModel
                $currentSemester = $this->semesterModel->getCurrentSemester();
                if ($currentSemester) {
                    $this->integrityModel->recalculateAllScores($currentSemester['id']);
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
            log_message('error', 'Upload Integritas Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process Integrity file and auto-fill data based on existing database records
     */
    private function processIntegrityFile($file)
    {
        $spreadsheet = IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Expected columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, KEHADIRAN MENGAJAR, JUMLAH MK DI AMPU
        $expectedHeaders = ['NO', 'NAMA DOSEN', 'NIP/NPT/NPPPK', 'ASAL PRODI', 'KEHADIRAN MENGAJAR', 'JUMLAH MK DI AMPU'];

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
                $result = $this->processIntegrityRow($row, $currentSemester['id'], $i + 1);
                if ($result['success']) {
                    $processed++;
                } else {
                    $errors[] = "Baris " . ($i + 1) . ": " . $result['error'];
                }
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                log_message('error', "Error processing integrity row {$i}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ];
    }

    /**
     * Process a single integrity row with auto-fill capability
     */
    private function processIntegrityRow($row, $semesterId, $rowNumber)
    {
        // Map columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, KEHADIRAN MENGAJAR, JUMLAH MK DI AMPU
        $no = trim($row[0] ?? '');
        $namaDosen = trim($row[1] ?? '');
        $nip = preg_replace('/\s+/', '', trim($row[2] ?? ''));
        $asalProdi = trim($row[3] ?? '');
        $kehadiranMengajar = $row[4] ?? null;
        $jumlahMK = $row[5] ?? null;

        if (empty($nip)) {
            return ['success' => false, 'error' => 'NIP tidak boleh kosong'];
        }

        // Find lecturer by NIP
        $lecturer = $this->lecturerModel->where('nip', $nip)->first();
        if (!$lecturer) {
            return ['success' => false, 'error' => "Dosen dengan NIP {$nip} tidak ditemukan di database"];
        }

        // Auto-fill from existing integrity data if values are empty
        $existingIntegrity = $this->integrityModel
            ->where('lecturer_id', $lecturer['id'])
            ->where('semester_id', $semesterId)
            ->first();

        if ($existingIntegrity) {
            // Fill missing values from existing data
            if (empty($kehadiranMengajar) || $kehadiranMengajar === null) {
                $kehadiranMengajar = $existingIntegrity['teaching_attendance'];
            }
            if (empty($jumlahMK) || $jumlahMK === null) {
                $jumlahMK = $existingIntegrity['courses_taught'];
            }
        }

        // Validate numeric values
        if (!is_numeric($kehadiranMengajar) || $kehadiranMengajar < 0 || $kehadiranMengajar > 100) {
            return ['success' => false, 'error' => 'Kehadiran mengajar harus berupa angka 0-100'];
        }

        if (!is_numeric($jumlahMK) || $jumlahMK < 0) {
            return ['success' => false, 'error' => 'Jumlah MK harus berupa angka positif'];
        }

        // Prepare data for insertion/update using IntegrityModel
        $integrityData = [
            'teaching_attendance' => (int)$kehadiranMengajar,
            'courses_taught' => (int)$jumlahMK
        ];

        // Use IntegrityModel's updateLecturerIntegrity method (handles score calculation)
        $result = $this->integrityModel->updateLecturerIntegrity(
            $lecturer['id'],
            $semesterId,
            $integrityData,
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
