<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class SKPExcelService
{
    private $spreadsheet;
    private $worksheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * Export SKP data to Excel with professional styling
     */
    public function exportSKPData($skpData, $semester, $filters = [])
    {
        try {
            // Set document properties
            $this->setDocumentProperties($semester);

            // Create header
            $this->createHeader($semester, $filters);

            // Create data table
            $this->createDataTable($skpData);

            // Create summary statistics
            $this->createSummaryStatistics($skpData);

            // Apply styling
            $this->applyGlobalStyling();

            // Generate and download file
            $this->downloadFile($semester);
        } catch (\Exception $e) {
            log_message('error', 'SKP Excel export error: ' . $e->getMessage());
            throw new \Exception('Gagal mengekspor data SKP ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Set document properties
     */
    private function setDocumentProperties($semester)
    {
        $this->spreadsheet->getProperties()
            ->setCreator('SKP Dosen System')
            ->setLastModifiedBy('SKP Dosen System')
            ->setTitle('Data Master SKP Dosen')
            ->setSubject('Sistem Penilaian Kinerja (SKP) Dosen')
            ->setDescription('Data Master SKP Dosen Fakultas - Semester ' . $semester['year'] . '/' . ($semester['term'] === '1' ? 'Ganjil' : 'Genap'))
            ->setKeywords('SKP, Dosen, Penilaian, Kinerja')
            ->setCategory('Report');
    }

    /**
     * Create header section
     */
    private function createHeader($semester, $filters)
    {
        $this->worksheet->setTitle('Master SKP Dosen');

        // Main title
        $this->worksheet->setCellValue('A1', 'DATA MASTER SKP DOSEN FAKULTAS');
        $this->worksheet->mergeCells('A1:K1');

        // University name
        $this->worksheet->setCellValue('A2', 'Universitas Pembangunan Nasional "Veteran" Jawa Timur');
        $this->worksheet->mergeCells('A2:K2');

        // Semester info
        $semesterText = 'Semester ' . $semester['year'] . '/' . ($semester['term'] === '1' ? 'Ganjil' : 'Genap');
        $this->worksheet->setCellValue('A3', $semesterText);
        $this->worksheet->mergeCells('A3:K3');

        // Export date
        $this->worksheet->setCellValue('A4', 'Diekspor pada: ' . date('d/m/Y H:i:s'));
        $this->worksheet->mergeCells('A4:K4');

        // Filter info
        if (!empty($filters)) {
            $filterText = 'Filter: ';
            $filterParts = [];

            if (!empty($filters['position'])) {
                $filterParts[] = 'Jabatan: ' . $filters['position'];
            }
            if (!empty($filters['study_program'])) {
                $filterParts[] = 'Program Studi: ' . $this->formatStudyProgram($filters['study_program']);
            }
            if (!empty($filters['skp_category'])) {
                $filterParts[] = 'Kategori SKP: ' . $filters['skp_category'];
            }

            if (!empty($filterParts)) {
                $filterText .= implode(', ', $filterParts);
                $this->worksheet->setCellValue('A5', $filterText);
                $this->worksheet->mergeCells('A5:K5');
            }
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']]
        ];

        $this->worksheet->getStyle('A1:K5')->applyFromArray($headerStyle);
    }

    /**
     * Create data table
     */
    private function createDataTable($skpData)
    {
        $startRow = 7; // Start after header

        // Table headers
        $headers = [
            'A' => 'No',
            'B' => 'Nama Dosen',
            'C' => 'NIP',
            'D' => 'Jabatan',
            'E' => 'Program Studi',
            'F' => 'Integritas',
            'G' => 'Disiplin',
            'H' => 'Komitmen',
            'I' => 'Kerjasama',
            'J' => 'Orientasi',
            'K' => 'Nilai SKP',
            'L' => 'Kategori SKP'
        ];

        foreach ($headers as $col => $header) {
            $this->worksheet->setCellValue($col . $startRow, $header);
        }

        // Style table headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1976D2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $this->worksheet->getStyle('A' . $startRow . ':L' . $startRow)->applyFromArray($headerStyle);

        // Data rows
        $currentRow = $startRow + 1;
        foreach ($skpData as $index => $lecturer) {
            $this->worksheet->setCellValue('A' . $currentRow, $index + 1);
            $this->worksheet->setCellValue('B' . $currentRow, $lecturer['lecturer_name']);
            $this->worksheet->setCellValue('C' . $currentRow, $lecturer['nip']);
            $this->worksheet->setCellValue('D' . $currentRow, $lecturer['position']);
            $this->worksheet->setCellValue('E' . $currentRow, $this->formatStudyProgram($lecturer['study_program']));
            $this->worksheet->setCellValue('F' . $currentRow, (int)$lecturer['integrity_score']);
            $this->worksheet->setCellValue('G' . $currentRow, (int)$lecturer['discipline_score']);
            $this->worksheet->setCellValue('H' . $currentRow, (int)$lecturer['commitment_score']);
            $this->worksheet->setCellValue('I' . $currentRow, (int)$lecturer['cooperation_score']);
            $this->worksheet->setCellValue('J' . $currentRow, (int)$lecturer['orientation_score']);
            $this->worksheet->setCellValue('K' . $currentRow, number_format((float)$lecturer['skp_score'], 1));
            $this->worksheet->setCellValue('L' . $currentRow, $lecturer['skp_category']);

            // Apply row styling with alternating colors
            $rowStyle = [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $index % 2 === 0 ? 'FFFFFF' : 'F8F9FA']]
            ];

            $this->worksheet->getStyle('A' . $currentRow . ':L' . $currentRow)->applyFromArray($rowStyle);

            // Color-code SKP category
            $categoryColor = $this->getCategoryColor($lecturer['skp_category']);
            $categoryStyle = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $categoryColor]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]
            ];

            $this->worksheet->getStyle('L' . $currentRow)->applyFromArray($categoryStyle);

            // Color-code SKP score
            $this->worksheet->getStyle('K' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $this->getScoreColor($lecturer['skp_score'])]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $currentRow++;
        }

        // Auto-resize columns
        foreach (range('A', 'L') as $col) {
            $this->worksheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create summary statistics
     */
    private function createSummaryStatistics($skpData)
    {
        if (empty($skpData)) return;

        $startRow = count($skpData) + 10; // Start after data table with some spacing

        // Summary title
        $this->worksheet->setCellValue('A' . $startRow, 'RINGKASAN STATISTIK');
        $this->worksheet->mergeCells('A' . $startRow . ':L' . $startRow);

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE0B2']]
        ];

        $this->worksheet->getStyle('A' . $startRow . ':L' . $startRow)->applyFromArray($titleStyle);

        $currentRow = $startRow + 2;

        // Calculate statistics
        $totalLecturers = count($skpData);
        $avgIntegrity = array_sum(array_column($skpData, 'integrity_score')) / $totalLecturers;
        $avgDiscipline = array_sum(array_column($skpData, 'discipline_score')) / $totalLecturers;
        $avgCommitment = array_sum(array_column($skpData, 'commitment_score')) / $totalLecturers;
        $avgCooperation = array_sum(array_column($skpData, 'cooperation_score')) / $totalLecturers;
        $avgOrientation = array_sum(array_column($skpData, 'orientation_score')) / $totalLecturers;
        $avgSKP = array_sum(array_column($skpData, 'skp_score')) / $totalLecturers;

        // Category distribution
        $categories = array_count_values(array_column($skpData, 'skp_category'));

        // Component averages
        $this->worksheet->setCellValue('A' . $currentRow, 'Rata-rata Komponen Penilaian:');
        $this->worksheet->mergeCells('A' . $currentRow . ':C' . $currentRow);

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Integritas:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgIntegrity, 1));

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Disiplin:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgDiscipline, 1));

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Komitmen:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgCommitment, 1));

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Kerjasama:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgCooperation, 1));

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Orientasi Pelayanan:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgOrientation, 1));

        $currentRow += 2;

        // Overall SKP statistics
        $this->worksheet->setCellValue('A' . $currentRow, 'Statistik SKP Keseluruhan:');
        $this->worksheet->mergeCells('A' . $currentRow . ':C' . $currentRow);

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Total Dosen:');
        $this->worksheet->setCellValue('C' . $currentRow, $totalLecturers);

        $currentRow++;
        $this->worksheet->setCellValue('B' . $currentRow, 'Rata-rata Nilai SKP:');
        $this->worksheet->setCellValue('C' . $currentRow, number_format($avgSKP, 1));

        $currentRow += 2;

        // Category distribution
        $this->worksheet->setCellValue('A' . $currentRow, 'Distribusi Kategori:');
        $this->worksheet->mergeCells('A' . $currentRow . ':C' . $currentRow);

        $currentRow++;
        foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $category) {
            $count = $categories[$category] ?? 0;
            $percentage = $totalLecturers > 0 ? ($count / $totalLecturers) * 100 : 0;

            $this->worksheet->setCellValue('B' . $currentRow, $category . ':');
            $this->worksheet->setCellValue('C' . $currentRow, $count . ' (' . number_format($percentage, 1) . '%)');

            // Color-code category
            $categoryColor = $this->getCategoryColor($category);
            $this->worksheet->getStyle('B' . $currentRow . ':C' . $currentRow)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $categoryColor]],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ]);

            $currentRow++;
        }
    }

    /**
     * Apply global styling
     */
    private function applyGlobalStyling()
    {
        // Set default font
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // Set row heights
        $this->worksheet->getDefaultRowDimension()->setRowHeight(20);

        // Set specific row heights for headers
        for ($i = 1; $i <= 5; $i++) {
            $this->worksheet->getRowDimension($i)->setRowHeight(25);
        }

        // Set page orientation and margins
        $this->worksheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $this->worksheet->getPageMargins()
            ->setTop(0.5)
            ->setRight(0.5)
            ->setBottom(0.5)
            ->setLeft(0.5);

        // Set print area
        $highestRow = $this->worksheet->getHighestRow();
        $this->worksheet->getPageSetup()->setPrintArea('A1:L' . $highestRow);

        // Add footer
        $this->worksheet->getHeaderFooter()
            ->setOddFooter('&L&B' . 'SKP Dosen System' . '&RHalaman &P dari &N');
    }

    /**
     * Download the Excel file
     */
    private function downloadFile($semester)
    {
        $filename = 'skp_master_data_' . $semester['year'] . '_' . $semester['term'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Format study program for display
     */
    private function formatStudyProgram($program)
    {
        $programs = [
            'bisnis_digital' => 'Bisnis Digital',
            'informatika' => 'Informatika',
            'sistem_informasi' => 'Sistem Informasi',
            'sains_data' => 'Sains Data',
            'magister_teknologi_informasi' => 'Magister TI'
        ];

        return $programs[$program] ?? $program;
    }

    /**
     * Get category color for styling
     */
    private function getCategoryColor($category)
    {
        return match ($category) {
            'Sangat Baik' => '4CAF50',  // Green
            'Baik' => '2196F3',         // Blue
            'Cukup' => 'FF9800',        // Orange
            'Kurang' => 'F44336',       // Red
            default => '9E9E9E'         // Grey
        };
    }

    /**
     * Get score color for styling
     */
    private function getScoreColor($score)
    {
        if ($score >= 88) return '4CAF50';      // Green
        if ($score >= 76) return '2196F3';      // Blue
        if ($score >= 61) return 'FF9800';      // Orange
        return 'F44336';                        // Red
    }
}
