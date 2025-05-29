<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExcelExportService
{
    private $spreadsheet;
    private $worksheet;

    // Position hierarchy for sorting
    private $positionHierarchy = [
        'DEKAN' => 1,
        'WAKIL DEKAN I' => 2,
        'WAKIL DEKAN II' => 3,
        'WAKIL DEKAN III' => 4,
        'KOORPRODI IF' => 5,
        'KOORPRODI SI' => 6,
        'KOORPRODI SD' => 7,
        'KOORPRODI BD' => 8,
        'KOORPRODI MTI' => 9,
        'Ka Lab SCR' => 10,
        'Ka Lab PPSTI' => 11,
        'Ka Lab SOLUSI' => 12,
        'Ka Lab MSI' => 13,
        'Ka Lab Sains Data' => 14,
        'Ka Lab BISDI' => 15,
        'Ka Lab MTI' => 16,
        'Ka UPT TIK' => 17,
        'Ka UPA PKK' => 18,
        'Ka Pengembangan Pembelajaran LPMPP' => 19,
        'PPMB' => 20,
        'KOORDINATOR PUSAT KARIR DAN TRACER STUDY' => 21,
        'LSP UPNVJT' => 22,
        'UPT TIK' => 23,
        'Dosen Prodi' => 24
    ];

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Export data dosen ke Excel dengan desain profesional
     */
    public function exportLecturers($lecturers, $searchTerm = null, $totalRecords = 0)
    {
        // Sort lecturers by position hierarchy first, then by name
        $sortedLecturers = $this->sortLecturersByHierarchy($lecturers);

        // Setup halaman
        $this->setupPageLayout();

        // Buat header dokumen
        $this->createDocumentHeader($searchTerm, $totalRecords);

        // Buat header tabel
        $this->createTableHeader();

        // Isi data
        $this->fillLecturerData($sortedLecturers);

        // Tambahkan footer
        $this->createFooter($totalRecords);

        // Auto-size kolom
        $this->autoSizeColumns();

        return $this->generateFile();
    }

    /**
     * Sort lecturers by position hierarchy
     */
    private function sortLecturersByHierarchy($lecturers)
    {
        usort($lecturers, function ($a, $b) {
            $positionA = $a['position'] ?? '';
            $positionB = $b['position'] ?? '';

            $hierarchyA = $this->positionHierarchy[$positionA] ?? 999;
            $hierarchyB = $this->positionHierarchy[$positionB] ?? 999;

            // First sort by position hierarchy
            if ($hierarchyA !== $hierarchyB) {
                return $hierarchyA - $hierarchyB;
            }

            // If same hierarchy level, sort by name
            return strcmp($a['name'] ?? '', $b['name'] ?? '');
        });

        return $lecturers;
    }

    /**
     * Setup layout halaman
     */
    private function setupPageLayout()
    {
        // Set orientasi landscape
        $this->worksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Set ukuran kertas A4
        $this->worksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // Set margin
        $this->worksheet->getPageMargins()->setTop(0.75);
        $this->worksheet->getPageMargins()->setRight(0.25);
        $this->worksheet->getPageMargins()->setLeft(0.25);
        $this->worksheet->getPageMargins()->setBottom(0.75);
    }

    /**
     * Buat header dokumen yang menarik
     */
    private function createDocumentHeader($searchTerm, $totalRecords)
    {
        // Logo atau header institusi (baris 1-3)
        $this->worksheet->mergeCells('A1:E3');
        $this->worksheet->setCellValue('A1', 'UNIVERSITAS PEMBANGUNAN NASIONAL "VETERAN" JAWA TIMUR');

        // Style header utama
        $this->worksheet->getStyle('A1:E3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '1F4E79']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F4FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1F4E79']
                ]
            ]
        ]);

        // Sub header (baris 4)
        $this->worksheet->mergeCells('A4:E4');
        $this->worksheet->setCellValue('A4', 'FAKULTAS ILMU KOMPUTER');
        $this->worksheet->getStyle('A4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1F4E79']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Judul laporan (baris 5)
        $this->worksheet->mergeCells('A5:E5');
        $title = 'DATA DOSEN FAKULTAS ILMU KOMPUTER';
        if ($searchTerm) {
            $title .= " (Pencarian: \"{$searchTerm}\")";
        }
        $this->worksheet->setCellValue('A5', $title);
        $this->worksheet->getStyle('A5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Info tanggal dan total (baris 6)
        $this->worksheet->mergeCells('A6:E6');
        $infoText = 'Tanggal Export: ' . date('d F Y H:i:s') . ' | Total Data: ' . number_format($totalRecords) . ' dosen';
        $this->worksheet->setCellValue('A6', $infoText);
        $this->worksheet->getStyle('A6')->applyFromArray([
            'font' => [
                'size' => 10,
                'italic' => true,
                'color' => ['rgb' => '666666']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Baris kosong
        $this->worksheet->getRowDimension(7)->setRowHeight(10);
    }

    /**
     * Buat header tabel dengan styling menarik
     */
    private function createTableHeader()
    {
        $headers = [
            'A8' => 'No',
            'B8' => 'NIP',
            'C8' => 'Nama Dosen',
            'D8' => 'Jabatan',
            'E8' => 'Program Studi'
        ];

        foreach ($headers as $cell => $value) {
            $this->worksheet->setCellValue($cell, $value);
        }

        // Style header tabel
        $this->worksheet->getStyle('A8:E8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2F5597']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]
        ]);

        // Set tinggi baris header
        $this->worksheet->getRowDimension(8)->setRowHeight(25);
    }

    /**
     * Isi data dosen dengan formatting yang rapi
     */
    private function fillLecturerData($lecturers)
    {
        $row = 9; // Mulai dari baris 9
        $no = 1;

        foreach ($lecturers as $lecturer) {
            // Kolom No
            $this->worksheet->setCellValue("A{$row}", $no);

            // Kolom NIP
            $this->worksheet->setCellValue("B{$row}", $lecturer['nip']);

            // Kolom Nama
            $this->worksheet->setCellValue("C{$row}", $lecturer['name']);

            // Kolom Jabatan
            $this->worksheet->setCellValue("D{$row}", $lecturer['position']);

            // Kolom Program Studi
            $studyProgram = $this->formatStudyProgram($lecturer['study_program'] ?? null);
            $this->worksheet->setCellValue("E{$row}", $studyProgram);

            // Style baris data
            $this->styleDataRow($row, $no);

            $row++;
            $no++;
        }

        // Border untuk seluruh tabel data
        $lastRow = $row - 1;
        $this->worksheet->getStyle("A8:E{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Style untuk baris data
     */
    private function styleDataRow($row, $no)
    {
        // Alternating row colors
        $fillColor = ($no % 2 == 0) ? 'F8F9FA' : 'FFFFFF';

        $this->worksheet->getStyle("A{$row}:E{$row}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $fillColor]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Alignment khusus per kolom
        $this->worksheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $this->worksheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NIP
        $this->worksheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Nama
        $this->worksheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Jabatan
        $this->worksheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Program Studi

        // Set tinggi baris
        $this->worksheet->getRowDimension($row)->setRowHeight(20);
    }

    /**
     * Format program studi
     */
    private function formatStudyProgram($studyProgram)
    {
        if (empty($studyProgram)) {
            return '-';
        }

        $programs = [
            'bisnis_digital' => 'Bisnis Digital',
            'informatika' => 'Informatika',
            'sistem_informasi' => 'Sistem Informasi',
            'sains_data' => 'Sains Data',
            'magister_teknologi_informasi' => 'Magister Teknologi Informasi'
        ];

        return $programs[$studyProgram] ?? ucwords(str_replace('_', ' ', $studyProgram));
    }

    /**
     * Tentukan status berdasarkan jabatan dan program studi
     */
    private function determineStatus($position, $studyProgram = null)
    {
        $leadershipPositions = ['DEKAN', 'WAKIL DEKAN I', 'WAKIL DEKAN II', 'WAKIL DEKAN III'];

        if (in_array($position, $leadershipPositions)) {
            return 'Pimpinan Fakultas';
        }

        if (
            strpos($position, 'KOORPRODI') !== false || strpos($position, 'Ka Lab') !== false ||
            strpos($position, 'Ka UPT') !== false || strpos($position, 'Ka UPA') !== false ||
            in_array($position, ['PPMB', 'KOORDINATOR PUSAT KARIR DAN TRACER STUDY', 'LSP UPNVJT', 'UPT TIK', 'Ka Pengembangan Pembelajaran LPMPP'])
        ) {
            return 'Koordinator';
        }

        // For 'Dosen Prodi', show the study program
        if ($position === 'Dosen Prodi' && !empty($studyProgram)) {
            return $this->formatStudyProgram($studyProgram);
        }

        return 'Dosen';
    }

    /**
     * Buat footer dokumen
     */
    private function createFooter($totalRecords)
    {
        $lastDataRow = $this->worksheet->getHighestRow();
        $footerRow = $lastDataRow + 2;

        // Summary
        $this->worksheet->mergeCells("A{$footerRow}:E{$footerRow}");
        $this->worksheet->setCellValue("A{$footerRow}", "Total Data Dosen: {$totalRecords} orang");
        $this->worksheet->getStyle("A{$footerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Tanda tangan/keterangan
        $signRow = $footerRow + 3;
        $this->worksheet->mergeCells("C{$signRow}:E{$signRow}");
        $this->worksheet->setCellValue("C{$signRow}", "Surabaya, " . date('d F Y'));
        $this->worksheet->getStyle("C{$signRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $signRow2 = $signRow + 1;
        $this->worksheet->mergeCells("C{$signRow2}:E{$signRow2}");
        $this->worksheet->setCellValue("C{$signRow2}", "Dekan Fakultas Ilmu Komputer");
        $this->worksheet->getStyle("C{$signRow2}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    /**
     * Auto-size kolom
     */
    private function autoSizeColumns()
    {
        // Set lebar kolom manual untuk hasil yang lebih baik
        $this->worksheet->getColumnDimension('A')->setWidth(8);   // No
        $this->worksheet->getColumnDimension('B')->setWidth(20);  // NIP
        $this->worksheet->getColumnDimension('C')->setWidth(35);  // Nama (lebih lebar)
        $this->worksheet->getColumnDimension('D')->setWidth(30);  // Jabatan (lebih lebar)
        $this->worksheet->getColumnDimension('E')->setWidth(25);  // Program Studi
    }

    /**
     * Generate file Excel
     */
    private function generateFile()
    {
        $writer = new Xlsx($this->spreadsheet);

        // Set metadata
        $this->spreadsheet->getProperties()
            ->setCreator("SKP Dosen System")
            ->setLastModifiedBy("SKP Dosen System")
            ->setTitle("Data Dosen Fakultas Ilmu Komputer")
            ->setSubject("Laporan Data Dosen")
            ->setDescription("Export data dosen dari sistem SKP Dosen")
            ->setKeywords("dosen, fakultas, ilmu komputer, upn veteran jatim")
            ->setCategory("Laporan");

        return $writer;
    }

    /**
     * Get highest row for calculation
     */
    private function getHighestRow()
    {
        return $this->worksheet->getHighestRow();
    }
}
