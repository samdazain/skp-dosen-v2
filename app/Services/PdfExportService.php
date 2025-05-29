<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfExportService
{
    private $dompdf;
    private $options;

    // Position hierarchy for sorting (same as Excel)
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
        $this->options = new Options();
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isPhpEnabled', true);
        $this->options->set('isRemoteEnabled', true);

        $this->dompdf = new Dompdf($this->options);
    }

    /**
     * Export data dosen ke PDF dengan desain profesional
     */
    public function exportLecturers($lecturers, $searchTerm = null, $totalRecords = 0)
    {
        // Sort lecturers by position hierarchy
        $sortedLecturers = $this->sortLecturersByHierarchy($lecturers);

        // Generate HTML content
        $html = $this->generateHtmlContent($sortedLecturers, $searchTerm, $totalRecords);

        // Load HTML to Dompdf
        $this->dompdf->loadHtml($html);

        // Set paper size and orientation
        $this->dompdf->setPaper('A4', 'landscape');

        // Render PDF
        $this->dompdf->render();

        return $this->dompdf;
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
     * Generate HTML content for PDF
     */
    private function generateHtmlContent($lecturers, $searchTerm, $totalRecords)
    {
        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen Fakultas Ilmu Komputer</title>
    <style>
        ' . $this->getCssStyles() . '
    </style>
</head>
<body>
    <div class="container">
        ' . $this->generateHeader($searchTerm, $totalRecords) . '
        ' . $this->generateTable($lecturers) . '
        ' . $this->generateFooter($totalRecords) . '
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * CSS styles for PDF
     */
    private function getCssStyles()
    {
        return '
        @page {
            margin: 1cm;
            size: A4 landscape;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F4E79;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1F4E79;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            color: #1F4E79;
            margin: 0 0 10px 0;
        }
        
        .header h3 {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin: 0 0 8px 0;
        }
        
        .header .info {
            font-size: 9px;
            color: #666;
            font-style: italic;
            margin: 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 20px 0;
            background: white;
        }
        
        .table th {
            background-color: #2F5597;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #2F5597;
            font-size: 9px;
        }
        
        .table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 8px;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tbody tr:nth-child(odd) {
            background-color: white;
        }
        
        .col-no {
            width: 5%;
            text-align: center;
        }
        
        .col-nip {
            width: 15%;
            text-align: center;
        }
        
        .col-name {
            width: 30%;
            text-align: left;
        }
        
        .col-position {
            width: 30%;
            text-align: left;
        }
        
        .col-program {
            width: 20%;
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .footer .summary {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 30px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        
        .signature {
            text-align: right;
            margin-top: 40px;
            font-size: 10px;
        }
        
        .signature .date {
            margin-bottom: 60px;
        }
        
        .signature .title {
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Prevent page breaks in table rows */
        .table tbody tr {
            page-break-inside: avoid;
        }
        
        /* Page break before footer if needed */
        .footer {
            page-break-before: auto;
        }
        ';
    }

    /**
     * Generate header section
     */
    private function generateHeader($searchTerm, $totalRecords)
    {
        $title = 'DATA DOSEN FAKULTAS ILMU KOMPUTER';
        if ($searchTerm) {
            $title .= " (Pencarian: \"{$searchTerm}\")";
        }

        $dateInfo = 'Tanggal Export: ' . date('d F Y H:i:s') . ' | Total Data: ' . number_format($totalRecords) . ' dosen';

        return '
        <div class="header">
            <h1>UNIVERSITAS PEMBANGUNAN NASIONAL "VETERAN" JAWA TIMUR</h1>
            <h2>FAKULTAS ILMU KOMPUTER</h2>
            <h3>' . htmlspecialchars($title) . '</h3>
            <p class="info">' . htmlspecialchars($dateInfo) . '</p>
        </div>';
    }

    /**
     * Generate table section
     */
    private function generateTable($lecturers)
    {
        $html = '
        <table class="table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-nip">NIP</th>
                    <th class="col-name">Nama Dosen</th>
                    <th class="col-position">Jabatan</th>
                    <th class="col-program">Program Studi</th>
                </tr>
            </thead>
            <tbody>';

        $no = 1;
        foreach ($lecturers as $lecturer) {
            $studyProgram = $this->formatStudyProgram($lecturer['study_program'] ?? null);

            $html .= '
                <tr>
                    <td class="col-no">' . $no . '</td>
                    <td class="col-nip">' . htmlspecialchars($lecturer['nip']) . '</td>
                    <td class="col-name">' . htmlspecialchars($lecturer['name']) . '</td>
                    <td class="col-position">' . htmlspecialchars($lecturer['position']) . '</td>
                    <td class="col-program">' . htmlspecialchars($studyProgram) . '</td>
                </tr>';
            $no++;
        }

        $html .= '
            </tbody>
        </table>';

        return $html;
    }

    /**
     * Generate footer section
     */
    private function generateFooter($totalRecords)
    {
        return '
        <div class="footer">
            <div class="summary">
                Total Data Dosen: ' . number_format($totalRecords) . ' orang
            </div>
            
            <div class="signature">
                <div class="date">Surabaya, ' . date('d F Y') . '</div>
                <div class="title">Dekan Fakultas Ilmu Komputer</div>
            </div>
        </div>';
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
     * Output PDF to browser
     */
    public function outputPdf($filename = 'document.pdf')
    {
        return $this->dompdf->output();
    }

    /**
     * Stream PDF to browser for download
     */
    public function streamPdf($filename = 'document.pdf')
    {
        $this->dompdf->stream($filename, ['Attachment' => true]);
    }
}
