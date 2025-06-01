<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class SKPPdfService
{
    private $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Export SKP data to PDF with professional styling
     */
    public function exportSKPData($skpData, $semester, $filters = [])
    {
        try {
            // Generate HTML content
            $html = $this->generateSKPHtml($skpData, $semester, $filters);

            // Load HTML to Dompdf
            $this->dompdf->loadHtml($html);

            // Set paper size and orientation
            $this->dompdf->setPaper('A4', 'landscape');

            // Render PDF
            $this->dompdf->render();

            // Generate filename
            $filename = 'skp_master_data_' . $semester['year'] . '_' . $semester['term'] . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // Output PDF
            $this->dompdf->stream($filename, ['Attachment' => true]);
        } catch (\Exception $e) {
            log_message('error', 'SKP PDF export error: ' . $e->getMessage());
            throw new \Exception('Gagal mengekspor data SKP ke PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML content for PDF
     */
    private function generateSKPHtml($skpData, $semester, $filters = [])
    {
        $semesterText = $semester['year'] . '/' . ($semester['term'] === '1' ? 'Ganjil' : 'Genap');

        // Calculate statistics
        $stats = $this->calculateStatistics($skpData);

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Master SKP Dosen</title>
    <style>
        ' . $this->getStyles() . '
    </style>
</head>
<body>
    <div class="header">
        <div class="university-logo">
            <div class="logo-placeholder">SKP</div>
        </div>
        <div class="header-text">
            <h1>DATA MASTER SKP DOSEN FAKULTAS</h1>
            <h2>Universitas Pembangunan Nasional "Veteran" Jawa Timur</h2>
            <h3>Semester ' . $semesterText . '</h3>
            <p class="export-info">Diekspor pada: ' . date('d/m/Y H:i:s') . '</p>
        </div>
    </div>';

        // Add filter information if any
        if (!empty($filters)) {
            $html .= '<div class="filter-info">
                <h4>Filter yang Diterapkan:</h4>
                <ul>';

            if (!empty($filters['position'])) {
                $html .= '<li><strong>Jabatan:</strong> ' . htmlspecialchars($filters['position']) . '</li>';
            }
            if (!empty($filters['study_program'])) {
                $html .= '<li><strong>Program Studi:</strong> ' . htmlspecialchars($this->formatStudyProgram($filters['study_program'])) . '</li>';
            }
            if (!empty($filters['skp_category'])) {
                $html .= '<li><strong>Kategori SKP:</strong> ' . htmlspecialchars($filters['skp_category']) . '</li>';
            }

            $html .= '</ul></div>';
        }

        // Summary statistics
        $html .= '<div class="summary-section">
            <h4>Ringkasan Statistik</h4>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Total Dosen:</span>
                    <span class="summary-value">' . $stats['total'] . '</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Rata-rata SKP:</span>
                    <span class="summary-value">' . number_format($stats['avg_skp'], 1) . '</span>
                </div>
                <div class="summary-item excellent">
                    <span class="summary-label">Sangat Baik:</span>
                    <span class="summary-value">' . $stats['categories']['Sangat Baik'] . ' (' . number_format($stats['percentages']['Sangat Baik'], 1) . '%)</span>
                </div>
                <div class="summary-item good">
                    <span class="summary-label">Baik:</span>
                    <span class="summary-value">' . $stats['categories']['Baik'] . ' (' . number_format($stats['percentages']['Baik'], 1) . '%)</span>
                </div>
                <div class="summary-item fair">
                    <span class="summary-label">Cukup:</span>
                    <span class="summary-value">' . $stats['categories']['Cukup'] . ' (' . number_format($stats['percentages']['Cukup'], 1) . '%)</span>
                </div>
                <div class="summary-item poor">
                    <span class="summary-label">Kurang:</span>
                    <span class="summary-value">' . $stats['categories']['Kurang'] . ' (' . number_format($stats['percentages']['Kurang'], 1) . '%)</span>
                </div>
            </div>
        </div>';

        // Data table
        $html .= '<div class="table-section">
            <table class="data-table">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Dosen</th>
                        <th rowspan="2">NIP</th>
                        <th rowspan="2">Jabatan</th>
                        <th rowspan="2">Program Studi</th>
                        <th colspan="5">Komponen Penilaian</th>
                        <th rowspan="2">Nilai SKP</th>
                        <th rowspan="2">Kategori</th>
                    </tr>
                    <tr>
                        <th>INT</th>
                        <th>DIS</th>
                        <th>KOM</th>
                        <th>KER</th>
                        <th>ORI</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($skpData as $index => $lecturer) {
            $skpScore = (float)$lecturer['skp_score'];
            $category = $lecturer['skp_category'];
            $categoryClass = $this->getCategoryClass($category);

            $html .= '<tr>
                <td class="text-center">' . ($index + 1) . '</td>
                <td class="lecturer-name">' . htmlspecialchars($lecturer['lecturer_name']) . '</td>
                <td class="text-center">' . htmlspecialchars($lecturer['nip']) . '</td>
                <td class="text-center position">' . htmlspecialchars($lecturer['position']) . '</td>
                <td class="text-center">' . htmlspecialchars($this->formatStudyProgram($lecturer['study_program'])) . '</td>
                <td class="text-center component-score">' . (int)$lecturer['integrity_score'] . '</td>
                <td class="text-center component-score">' . (int)$lecturer['discipline_score'] . '</td>
                <td class="text-center component-score">' . (int)$lecturer['commitment_score'] . '</td>
                <td class="text-center component-score">' . (int)$lecturer['cooperation_score'] . '</td>
                <td class="text-center component-score">' . (int)$lecturer['orientation_score'] . '</td>
                <td class="text-center skp-score ' . $categoryClass . '">' . number_format($skpScore, 1) . '</td>
                <td class="text-center category ' . $categoryClass . '">' . htmlspecialchars($category) . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
        </div>';

        // Component averages
        $html .= '<div class="component-averages">
            <h4>Rata-rata Komponen Penilaian</h4>
            <div class="component-grid">
                <div class="component-item">
                    <span class="component-label">Integritas:</span>
                    <span class="component-value">' . number_format($stats['avg_integrity'], 1) . '</span>
                </div>
                <div class="component-item">
                    <span class="component-label">Disiplin:</span>
                    <span class="component-value">' . number_format($stats['avg_discipline'], 1) . '</span>
                </div>
                <div class="component-item">
                    <span class="component-label">Komitmen:</span>
                    <span class="component-value">' . number_format($stats['avg_commitment'], 1) . '</span>
                </div>
                <div class="component-item">
                    <span class="component-label">Kerjasama:</span>
                    <span class="component-value">' . number_format($stats['avg_cooperation'], 1) . '</span>
                </div>
                <div class="component-item">
                    <span class="component-label">Orientasi Pelayanan:</span>
                    <span class="component-value">' . number_format($stats['avg_orientation'], 1) . '</span>
                </div>
            </div>
        </div>';

        $html .= '<div class="footer">
            <div class="footer-left">
                <p><strong>Sistem Penilaian Kinerja (SKP) Dosen</strong></p>
                <p>UPN "Veteran" Jawa Timur</p>
            </div>
            <div class="footer-right">
                <p>Halaman <span class="pagenum"></span></p>
                <p>Dokumen dihasilkan secara otomatis</p>
            </div>
        </div>

</body>
</html>';

        return $html;
    }

    /**
     * Get CSS styles for PDF
     */
    private function getStyles()
    {
        return '
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1976D2;
            padding-bottom: 10px;
        }
        
        .university-logo {
            float: left;
            width: 60px;
            height: 60px;
        }
        
        .logo-placeholder {
            font-size: 48px;
            color: #1976D2;
            text-align: center;
            line-height: 60px;
        }
        
        .header-text {
            margin-left: 70px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #1976D2;
        }
        
        .header h2 {
            font-size: 12px;
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .header h3 {
            font-size: 11px;
            margin: 0 0 5px 0;
            color: #666;
        }
        
        .export-info {
            font-size: 8px;
            color: #888;
            margin: 5px 0 0 0;
        }
        
        .filter-info {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #1976D2;
        }
        
        .filter-info h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
            color: #1976D2;
        }
        
        .filter-info ul {
            margin: 0;
            padding-left: 15px;
        }
        
        .filter-info li {
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        .summary-section {
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        
        .summary-section h4 {
            margin: 0 0 10px 0;
            font-size: 11px;
            color: #1976D2;
            text-align: center;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
        }
        
        .summary-item {
            text-align: center;
            padding: 8px 4px;
            border-radius: 4px;
            background-color: #e3f2fd;
        }
        
        .summary-item.excellent { background-color: #e8f5e8; }
        .summary-item.good { background-color: #e3f2fd; }
        .summary-item.fair { background-color: #fff3e0; }
        .summary-item.poor { background-color: #ffebee; }
        
        .summary-label {
            display: block;
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .summary-value {
            display: block;
            font-size: 8px;
            font-weight: bold;
        }
        
        .table-section {
            margin-bottom: 15px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
        }
        
        .data-table th {
            background-color: #1976D2;
            color: white;
            padding: 6px 3px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #0d47a1;
        }
        
        .data-table td {
            padding: 4px 3px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-center {
            text-align: center;
        }
        
        .lecturer-name {
            font-weight: bold;
            max-width: 80px;
            word-wrap: break-word;
        }
        
        .position {
            font-size: 6px;
            max-width: 60px;
            word-wrap: break-word;
        }
        
        .component-score {
            font-weight: bold;
            color: #1976D2;
        }
        
        .skp-score {
            font-weight: bold;
            font-size: 8px;
        }
        
        .category {
            font-weight: bold;
            font-size: 6px;
        }
        
        .excellent {
            background-color: #4caf50 !important;
            color: white !important;
        }
        
        .good {
            background-color: #2196f3 !important;
            color: white !important;
        }
        
        .fair {
            background-color: #ff9800 !important;
            color: white !important;
        }
        
        .poor {
            background-color: #f44336 !important;
            color: white !important;
        }
        
        .component-averages {
            margin-bottom: 15px;
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 4px;
        }
        
        .component-averages h4 {
            margin: 0 0 10px 0;
            font-size: 11px;
            color: #1976D2;
            text-align: center;
        }
        
        .component-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        
        .component-item {
            text-align: center;
            padding: 6px 4px;
            background-color: #e3f2fd;
            border-radius: 4px;
        }
        
        .component-label {
            display: block;
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .component-value {
            display: block;
            font-size: 8px;
            font-weight: bold;
            color: #1976D2;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
            padding: 5px 15px;
            font-size: 7px;
        }
        
        .footer-left {
            float: left;
        }
        
        .footer-right {
            float: right;
            text-align: right;
        }
        
        .footer p {
            margin: 0;
            line-height: 1.2;
        }
        
        .pagenum:before {
            content: counter(page);
        }
        ';
    }

    /**
     * Calculate statistics for the data
     */
    private function calculateStatistics($skpData)
    {
        if (empty($skpData)) {
            return [
                'total' => 0,
                'avg_skp' => 0,
                'avg_integrity' => 0,
                'avg_discipline' => 0,
                'avg_commitment' => 0,
                'avg_cooperation' => 0,
                'avg_orientation' => 0,
                'categories' => ['Sangat Baik' => 0, 'Baik' => 0, 'Cukup' => 0, 'Kurang' => 0],
                'percentages' => ['Sangat Baik' => 0, 'Baik' => 0, 'Cukup' => 0, 'Kurang' => 0]
            ];
        }

        $total = count($skpData);
        $categories = array_count_values(array_column($skpData, 'skp_category'));
        $percentages = [];

        foreach (['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $cat) {
            $count = $categories[$cat] ?? 0;
            $percentages[$cat] = $total > 0 ? ($count / $total) * 100 : 0;
        }

        return [
            'total' => $total,
            'avg_skp' => array_sum(array_column($skpData, 'skp_score')) / $total,
            'avg_integrity' => array_sum(array_column($skpData, 'integrity_score')) / $total,
            'avg_discipline' => array_sum(array_column($skpData, 'discipline_score')) / $total,
            'avg_commitment' => array_sum(array_column($skpData, 'commitment_score')) / $total,
            'avg_cooperation' => array_sum(array_column($skpData, 'cooperation_score')) / $total,
            'avg_orientation' => array_sum(array_column($skpData, 'orientation_score')) / $total,
            'categories' => array_merge(['Sangat Baik' => 0, 'Baik' => 0, 'Cukup' => 0, 'Kurang' => 0], $categories),
            'percentages' => $percentages
        ];
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
     * Get CSS class for category styling
     */
    private function getCategoryClass($category)
    {
        return match ($category) {
            'Sangat Baik' => 'excellent',
            'Baik' => 'good',
            'Cukup' => 'fair',
            'Kurang' => 'poor',
            default => ''
        };
    }
}
