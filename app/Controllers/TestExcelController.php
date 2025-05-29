<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class TestExcelController extends BaseController
{
    public function testReadExcel()
    {
        // Path ke file Excel test Anda
        $filePath = FCPATH . '"D:\KULIAH\SEMESTER6\PKL\Project\PID_DOSEN_FASILKOM_EDITx.xlsx"'; // Sesuaikan path

        if (!file_exists($filePath)) {
            return "File tidak ditemukan: {$filePath}";
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            echo "<h3>Test Reading Excel File</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Row</th><th>Column B (Name)</th><th>Column C (NIP)</th><th>Column D (Study Program)</th><th>Column E (Position)</th></tr>";

            for ($row = 6; $row <= 12; $row++) {
                $nameCell = $worksheet->getCell("B{$row}");
                $nipCell = $worksheet->getCell("C{$row}");
                $studyCell = $worksheet->getCell("D{$row}");
                $positionCell = $worksheet->getCell("E{$row}");

                $name = $this->getCellValue($nameCell);
                $nip = $this->getCellValue($nipCell);
                $study = $this->getCellValue($studyCell);
                $position = $this->getCellValue($positionCell);

                echo "<tr>";
                echo "<td>{$row}</td>";
                echo "<td>'{$name}'</td>";
                echo "<td>'{$nip}'</td>";
                echo "<td>'{$study}'</td>";
                echo "<td>'{$position}'</td>";
                echo "</tr>";
            }
            echo "</table>";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function getCellValue($cell)
    {
        try {
            $calculated = $cell->getCalculatedValue();
            $formatted = $cell->getFormattedValue();
            $raw = $cell->getValue();

            echo "<!-- Calculated: '{$calculated}', Formatted: '{$formatted}', Raw: '{$raw}' -->";

            return $calculated ?: $formatted ?: $raw ?: '';
        } catch (\Exception $e) {
            return 'ERROR: ' . $e->getMessage();
        }
    }
}
