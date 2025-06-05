<?php

namespace App\Controllers;

use App\Models\LecturerModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\HTTP\Files\UploadedFile;

class UploadController extends BaseController
{
    protected $lecturerModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->lecturerModel = new LecturerModel();
        helper($this->helpers);
    }

    public function debugExcel()
    {
        // Test data yang sama seperti di Excel
        $testData = [
            ['Prof. Dr. Ir. Novirina Hendrasarie, MT.', '19681126 199403 2 001', '–', 'DEKAN'],
            ['Dr. I Gede Susrama Mas Diyasa, ST., MT.', '19700619 202121 1 009', 'MAGISTER TEKNOLOGI INFORMASI', 'WAKIL DEKAN I'],
            ['Made Hanindia Prami Swari, S.Kom, M.Cs', '19890205 201803 2 001', 'INFORMATIKA', 'WAKIL DEKAN II'],
            // Add test cases for position cleaning
            ['Test Dosen 1', '12345678901234567890', 'INFORMATIKA', 'Dosen Prodi Informatika'],
            ['Test Dosen 2', '12345678901234567891', 'SISTEM INFORMASI', 'Dosen Prodi Sistem Informasi'],
            ['Test Dosen 3', '12345678901234567892', 'SAINS DATA', 'Dosen Prodi Sains Data'],
            ['Test Dosen 4', '12345678901234567893', 'BISNIS DIGITAL', 'Dosen Prodi Bisnis Digital'],
            ['Test Dosen 5', '12345678901234567894', 'MAGISTER TEKNOLOGI INFORMASI', 'Dosen Prodi Teknologi Informasi']
        ];

        echo "<h3>Debug Data Processing</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Original Name</th><th>Cleaned Name</th><th>Original NIP</th><th>Cleaned NIP</th><th>Original Position</th><th>Cleaned Position</th><th>Is Leadership</th></tr>";

        foreach ($testData as $index => $row) {
            $originalName = $row[0];
            $originalNip = $row[1];
            $originalStudy = $row[2];
            $originalPosition = $row[3];

            $cleanedName = $this->cleanText($originalName);
            $cleanedNip = $this->cleanNIP($originalNip);
            $cleanedStudy = $this->cleanText($originalStudy);
            $cleanedPosition = $this->cleanPosition($originalPosition);

            $isLeadership = $this->lecturerModel->isLeadershipPosition($cleanedPosition) ? 'YES' : 'NO';

            echo "<tr>";
            echo "<td>'{$originalName}'</td>";
            echo "<td>'{$cleanedName}'</td>";
            echo "<td>'{$originalNip}'</td>";
            echo "<td>'{$cleanedNip}'</td>";
            echo "<td>'{$originalPosition}'</td>";
            echo "<td>'{$cleanedPosition}'</td>";
            echo "<td>{$isLeadership}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function uploadDosen()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $validationRules = [
            'file_dosen' => [
                'rules' => 'uploaded[file_dosen]|max_size[file_dosen,5120]|ext_in[file_dosen,xlsx,xls]',
                'errors' => [
                    'uploaded' => 'File harus dipilih',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'File harus berformat Excel (.xlsx atau .xls)'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file_dosen');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        try {
            $result = $this->processExcelFile($file);

            if ($result['success']) {
                return redirect()->to('dashboard')
                    ->with('success', $result['message']);
            } else {
                return redirect()->back()
                    ->with('error', $result['message'])
                    ->with('errors', $result['errors'] ?? []);
            }
        } catch (\Exception $e) {
            log_message('error', 'Upload dosen error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    private function processExcelFile(UploadedFile $file)
    {
        $spreadsheet = IOFactory::load($file->getTempName());
        $worksheet = $spreadsheet->getActiveSheet();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $duplicates = [];
        $successData = []; // Track successful insertions

        // Get the highest row and column numbers
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        log_message('info', "Excel file loaded. Highest row: {$highestRow}, Highest column: {$highestColumn}");

        // Process data from row 7 to the end of data
        for ($row = 7; $row <= min($highestRow, 79); $row++) {
            // Get raw values first
            $nameCell = $worksheet->getCell("B{$row}");
            $nipCell = $worksheet->getCell("C{$row}");
            $studyProgramCell = $worksheet->getCell("D{$row}");
            $positionCell = $worksheet->getCell("E{$row}");

            // Try different methods to get the values
            $name = $this->getCellValue($nameCell);
            $nip = $this->getCellValue($nipCell);
            $studyProgram = $this->getCellValue($studyProgramCell);
            $position = $this->getCellValue($positionCell);

            // Debug logging for first few rows
            if ($row <= 10) {
                log_message('info', "Row {$row}: Name='{$name}', NIP='{$nip}', StudyProgram='{$studyProgram}', Position='{$position}'");
            }

            // Skip empty rows (check if all main fields are empty)
            if (empty($name) && empty($nip) && empty($position)) {
                continue;
            }

            // Clean the data
            $name = $this->cleanText($name);
            $nip = $this->cleanNIP($nip);
            $studyProgram = $this->cleanText($studyProgram);
            $position = $this->cleanPosition($position);

            // Additional logging after cleaning
            log_message('info', "Row {$row} after cleaning: Name='{$name}', NIP='{$nip}', StudyProgram='{$studyProgram}', Position='{$position}'");

            // Validate required fields manually first
            if (empty($name) || empty($nip) || empty($position)) {
                $errors[] = "Baris {$row}: Data tidak lengkap - Nama: '{$name}', NIP: '{$nip}', Jabatan: '{$position}'";
                $errorCount++;
                continue;
            }

            // Map study program
            $mappedStudyProgram = $this->mapStudyProgram($studyProgram);

            // Check for leadership position
            $isLeadership = $this->lecturerModel->isLeadershipPosition($position);

            // Prepare data
            $data = [
                'name' => $name,
                'nip' => $nip,
                'position' => $position,
                'study_program' => $isLeadership ? null : $mappedStudyProgram
            ];

            log_message('info', "Row {$row} prepared data: " . json_encode($data));

            // Use manual validation instead of CodeIgniter's validate method
            $validationErrors = $this->validateLecturerData($data, $isLeadership);

            if (!empty($validationErrors)) {
                $errors[] = "Baris {$row} ({$name}): " . implode(', ', $validationErrors);
                $errorCount++;
                continue;
            }

            // Check if NIP already exists
            $existingLecturer = $this->lecturerModel->where('nip', $nip)->first();
            if ($existingLecturer) {
                $duplicates[] = "Baris {$row}: NIP {$nip} ({$name}) sudah terdaftar";
                $errorCount++;
                continue;
            }

            // Insert data
            if ($this->lecturerModel->insert($data)) {
                $successCount++;
                $successData[] = [
                    'row' => $row,
                    'name' => $name,
                    'nip' => $nip,
                    'position' => $position
                ];
                log_message('info', "Successfully inserted: {$name} (NIP: {$nip})");
            } else {
                $dbErrors = $this->lecturerModel->errors();
                $errors[] = "Baris {$row} ({$name}): Database error - " . implode(', ', $dbErrors);
                $errorCount++;
            }
        }

        // Prepare detailed result message
        $message = $this->buildUploadMessage($successCount, $errorCount, $successData);

        $allErrors = array_merge($errors, $duplicates);

        return [
            'success' => $successCount > 0,
            'message' => $message,
            'duplicates' => $duplicates,
            'errors' => $allErrors,
            'success_count' => $successCount,
            'error_count' => $errorCount
        ];
    }

    // Add this new method to build detailed success message
    private function buildUploadMessage($successCount, $errorCount, $successData)
    {
        $message = "Upload Data Dosen Selesai!\n\n";

        if ($successCount > 0) {
            $message .= "✅ {$successCount} data berhasil ditambahkan:\n";
            foreach (array_slice($successData, 0, 5) as $data) { // Show first 5 successful entries
                $message .= "• {$data['name']} (NIP: {$data['nip']}) - {$data['position']}\n";
            }

            if (count($successData) > 5) {
                $remaining = count($successData) - 5;
                $message .= "• ... dan {$remaining} data lainnya\n";
            }
        }

        if ($errorCount > 0) {
            $message .= "\n❌ {$errorCount} data gagal diproses";
            $message .= "\nSilakan periksa detail error di bawah untuk informasi lebih lanjut.";
        }

        return $message;
    }

    // Add this new method for manual validation
    private function validateLecturerData($data, $isLeadership = false)
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors[] = 'Nama harus diisi';
        } elseif (strlen($data['name']) < 3) {
            $errors[] = 'Nama minimal 3 karakter';
        } elseif (strlen($data['name']) > 100) {
            $errors[] = 'Nama maksimal 100 karakter';
        }

        // Validate NIP
        if (empty($data['nip'])) {
            $errors[] = 'NIP harus diisi';
        } elseif (strlen($data['nip']) < 10) {
            $errors[] = 'NIP minimal 10 karakter';
        } elseif (strlen($data['nip']) > 30) {
            $errors[] = 'NIP maksimal 30 karakter';
        }

        // Validate position
        if (empty($data['position'])) {
            $errors[] = 'Jabatan harus diisi';
        }
        // Note: Position validation against array is removed to allow flexible positions

        // Validate study program for non-leadership positions
        if (!$isLeadership && !empty($data['study_program'])) {
            $validPrograms = ['bisnis_digital', 'informatika', 'sistem_informasi', 'sains_data', 'magister_teknologi_informasi'];
            if (!in_array($data['study_program'], $validPrograms)) {
                $errors[] = 'Program studi tidak valid';
            }
        }

        return $errors;
    }

    private function getCellValue($cell)
    {
        if (!$cell) {
            return '';
        }

        try {
            // Get the raw value first
            $rawValue = $cell->getValue();

            // If it's null or empty, try other methods
            if ($rawValue === null || $rawValue === '') {
                $calculatedValue = $cell->getCalculatedValue();
                $formattedValue = $cell->getFormattedValue();

                // Return the first non-empty value
                $value = $calculatedValue ?: $formattedValue ?: '';
            } else {
                $value = $rawValue;
            }

            // Convert to string and clean
            $stringValue = (string)$value;

            // Log the original and converted values for debugging
            if (!empty($stringValue)) {
                log_message('debug', "Cell value - Raw: '{$rawValue}', Final: '{$stringValue}'");
            }

            return $stringValue;
        } catch (\Exception $e) {
            log_message('error', 'Error reading cell value: ' . $e->getMessage());
            return '';
        }
    }

    private function cleanText($text)
    {
        if (empty($text)) {
            return '';
        }

        // Convert to string first
        $text = (string)$text;

        // Remove BOM and other invisible characters
        $text = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $text);

        // Trim whitespace
        $text = trim($text);

        // Replace multiple spaces with single space
        $text = preg_replace('/\s+/u', ' ', $text);

        // Remove specific problematic characters but keep essential punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s\.\-,()]/u', '', $text);

        return trim($text);
    }

    // New method to clean position specifically
    private function cleanPosition($position)
    {
        if (empty($position)) {
            return '';
        }

        // First apply general text cleaning
        $position = $this->cleanText($position);

        // Normalize "Dosen Prodi" positions to just "Dosen Prodi"
        $dosenProdiPatterns = [
            '/^Dosen Prodi Informatika$/i',
            '/^Dosen Prodi Sistem Informasi$/i',
            '/^Dosen Prodi Sains Data$/i',
            '/^Dosen Prodi Bisnis Digital$/i',
            '/^Dosen Prodi Teknologi Informasi$/i',
            '/^Dosen Prodi Magister Teknologi Informasi$/i'
        ];

        foreach ($dosenProdiPatterns as $pattern) {
            if (preg_match($pattern, $position)) {
                return 'Dosen Prodi';
            }
        }

        return $position;
    }

    private function cleanNIP($nip)
    {
        if (empty($nip)) {
            return '';
        }

        // Remove all non-numeric characters and spaces for NIP
        $nip = preg_replace('/[^0-9]/', '', $nip);

        return $nip;
    }

    private function mapStudyProgram($studyProgram)
    {
        if (empty($studyProgram) || $studyProgram === '–' || $studyProgram === '-') {
            return null;
        }

        $mapping = [
            'bisnis digital' => 'bisnis_digital',
            'bd' => 'bisnis_digital',
            'informatika' => 'informatika',
            'if' => 'informatika',
            'sistem informasi' => 'sistem_informasi',
            'si' => 'sistem_informasi',
            'sains data' => 'sains_data',
            'sd' => 'sains_data',
            'magister teknologi informasi' => 'magister_teknologi_informasi',
            'mti' => 'magister_teknologi_informasi'
        ];

        $normalized = strtolower(trim($studyProgram));
        return $mapping[$normalized] ?? null;
    }
}
