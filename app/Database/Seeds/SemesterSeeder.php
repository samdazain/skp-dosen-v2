<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        // Generate semesters for the last 3 years and next 2 years
        $startYear = $currentYear - 3;
        $endYear = $currentYear + 2;

        $semesters = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            // Semester Ganjil (Odd - Term 1): August to January
            $semesters[] = [
                'year' => $year,
                'term' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Semester Genap (Even - Term 2): February to July
            $semesters[] = [
                'year' => $year,
                'term' => '2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        // Sort semesters by year and term (newest first)
        usort($semesters, function ($a, $b) {
            if ($a['year'] == $b['year']) {
                return $b['term'] - $a['term']; // Term 2 before Term 1 for same year
            }
            return $b['year'] - $a['year']; // Newer years first
        });

        // Insert semesters in batches
        $this->db->table('semesters')->insertBatch($semesters);

        // Set current semester as active in session (optional)
        $this->setActiveSemester($currentYear, $currentMonth);

        echo "Semester seeder completed. Added " . count($semesters) . " semesters.\n";
    }

    /**
     * Determine and log the current active semester
     */
    private function setActiveSemester($year, $month)
    {
        // Determine current semester based on month
        // August (8) to January (1) = Ganjil (1)
        // February (2) to July (7) = Genap (2)

        if ($month >= 8) {
            // August to December = Ganjil of current year
            $currentTerm = '1';
            $currentYear = $year;
        } else {
            // January to July = depends on month
            if ($month >= 2) {
                // February to July = Genap of previous year
                $currentTerm = '2';
                $currentYear = $year - 1;
            } else {
                // January = Ganjil of previous year
                $currentTerm = '1';
                $currentYear = $year - 1;
            }
        }

        // Find the current semester ID
        $currentSemester = $this->db->table('semesters')
            ->where('year', $currentYear)
            ->where('term', $currentTerm)
            ->get()
            ->getRow();

        if ($currentSemester) {
            echo "Current active semester should be: Semester " .
                ($currentTerm == '1' ? 'Ganjil' : 'Genap') .
                " {$currentYear}/" . ($currentYear + 1) .
                " (ID: {$currentSemester->id})\n";
        }
    }
}
