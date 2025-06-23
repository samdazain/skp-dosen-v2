<?php

namespace App\Controllers;

use App\Models\DisciplineDataUploadModel;
use App\Models\LecturerModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\ResponseInterface;

class DisciplineDataUploadController extends BaseController
{
    protected $dataUploadModel;
    protected $lecturerModel;
    protected $disciplineModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->dataUploadModel = new DisciplineDataUploadModel();
        $this->lecturerModel = new LecturerModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Upload and process Discipline data from XLSX/CSV file
     */
    public function uploadDisiplin()
    {
        if (!$this->request->isAJAX() && !$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $file = $this->request->getFile('file_disiplin');

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
            $result = $this->processDisciplineFile($file);

            if ($result['success']) {
                $message = "Upload berhasil! {$result['processed']} data diproses";

                // Recalculate all scores after upload using DisciplineModel
                $currentSemester = $this->semesterModel->getCurrentSemester();
                if ($currentSemester) {
                    $this->disciplineModel->recalculateAllScores($currentSemester['id']);
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
            log_message('error', 'Upload Disiplin Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Process Discipline file and auto-fill data based on existing database records
     */
    private function processDisciplineFile($file)
    {
        $spreadsheet = IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Expected columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, ABSEN HARIAN (JUMLAH ALPHA), ABSEN SENAM PAGI (JUMLAH ALPHA), ABSEN UPACARA (JUMLAH ALPHA)
        $expectedHeaders = ['NO', 'NAMA DOSEN', 'NIP/NPT/NPPPK', 'ASAL PRODI', 'ABSEN HARIAN (JUMLAH ALPHA)', 'ABSEN SENAM PAGI (JUMLAH ALPHA)', 'ABSEN UPACARA (JUMLAH ALPHA)'];

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
                $result = $this->processDisciplineRow($row, $currentSemester['id'], $i + 1);
                if ($result['success']) {
                    $processed++;
                } else {
                    $errors[] = "Baris " . ($i + 1) . ": " . $result['error'];
                }
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                log_message('error', "Error processing discipline row {$i}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ];
    }

    /**
     * Process a single discipline row with auto-fill capability
     */
    private function processDisciplineRow($row, $semesterId, $rowNumber)
    {
        // Map columns: NO, NAMA DOSEN, NIP/NPT/NPPPK, ASAL PRODI, ABSEN HARIAN, ABSEN SENAM PAGI, ABSEN UPACARA
        $no = trim($row[0] ?? '');
        $namaDosen = trim($row[1] ?? '');
        $nip = preg_replace('/\s+/', '', trim($row[2] ?? ''));
        $asalProdi = trim($row[3] ?? '');
        $absenHarian = $row[4] ?? null;
        $absenSenamPagi = $row[5] ?? null;
        $absenUpacara = $row[6] ?? null;

        if (empty($nip)) {
            return ['success' => false, 'error' => 'NIP tidak boleh kosong'];
        }

        // Find lecturer by NIP
        $lecturer = $this->lecturerModel->where('nip', $nip)->first();
        if (!$lecturer) {
            return ['success' => false, 'error' => "Dosen dengan NIP {$nip} tidak ditemukan di database"];
        }

        // Auto-fill from existing discipline data if values are empty
        $existingDiscipline = $this->disciplineModel
            ->where('lecturer_id', $lecturer['id'])
            ->where('semester_id', $semesterId)
            ->first();

        if ($existingDiscipline) {
            // Fill missing values from existing data
            if (empty($absenHarian) || $absenHarian === null) {
                $absenHarian = $existingDiscipline['daily_absence'];
            }
            if (empty($absenSenamPagi) || $absenSenamPagi === null) {
                $absenSenamPagi = $existingDiscipline['exercise_morning_absence'];
            }
            if (empty($absenUpacara) || $absenUpacara === null) {
                $absenUpacara = $existingDiscipline['ceremony_absence'];
            }
        }

        // Validate numeric values
        if (!is_numeric($absenHarian) || $absenHarian < 0) {
            return ['success' => false, 'error' => 'Absen harian harus berupa angka positif atau nol'];
        }

        if (!is_numeric($absenSenamPagi) || $absenSenamPagi < 0) {
            return ['success' => false, 'error' => 'Absen senam pagi harus berupa angka positif atau nol'];
        }

        if (!is_numeric($absenUpacara) || $absenUpacara < 0) {
            return ['success' => false, 'error' => 'Absen upacara harus berupa angka positif atau nol'];
        }

        // Prepare data for insertion/update using DisciplineModel
        $disciplineData = [
            'daily_absence' => (int)$absenHarian,
            'exercise_morning_absence' => (int)$absenSenamPagi,
            'ceremony_absence' => (int)$absenUpacara
        ];

        // Use DisciplineModel's updateLecturerDiscipline method (handles score calculation)
        $result = $this->disciplineModel->updateLecturerDiscipline(
            $lecturer['id'],
            $semesterId,
            $disciplineData,
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
