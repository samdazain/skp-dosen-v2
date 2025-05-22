<?php

namespace App\Models;

use CodeIgniter\Model;

class SkpModel extends Model
{
    protected $table = 'skp'; // Change to your actual table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['lecturer_id', 'integrity', 'discipline', 'commitment', 'cooperation', 'service'];

    /**
     * Calculate the total score from component scores
     */
    public function calculateTotal($scores)
    {
        return round(($scores['integrity'] + $scores['discipline'] + $scores['commitment'] +
            $scores['cooperation'] + $scores['service']) / 5, 1);
    }

    /**
     * Get category based on total score
     */
    public function getCategory($total)
    {
        if ($total >= 91) return ['Sangat Baik', 'success'];
        if ($total >= 76) return ['Baik', 'primary'];
        if ($total >= 61) return ['Cukup', 'warning'];
        if ($total >= 51) return ['Kurang', 'danger'];
        return ['Buruk', 'dark'];
    }

    /**
     * Get dummy data for demonstration
     * In production, replace with actual database queries
     */
    public function getDummyData()
    {
        return [
            [
                'name' => 'Dr. Budi Santoso, M.Kom',
                'nip' => '197505152000121001',
                'study_program' => 'Informatika',
                'integrity' => 90,
                'discipline' => 85,
                'commitment' => 88,
                'cooperation' => 92,
                'service' => 87
            ],
            // ... other dummy data
        ];
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats($data)
    {
        $stats = [
            'sangat_baik' => 0,
            'baik' => 0,
            'cukup' => 0,
            'kurang' => 0
        ];

        foreach ($data as $item) {
            $total = $this->calculateTotal($item);
            list($category, $_) = $this->getCategory($total);

            switch ($category) {
                case 'Sangat Baik':
                    $stats['sangat_baik']++;
                    break;
                case 'Baik':
                    $stats['baik']++;
                    break;
                case 'Cukup':
                    $stats['cukup']++;
                    break;
                case 'Kurang':
                    $stats['kurang']++;
                    break;
            }
        }

        $total = count($data);
        return [
            'sangat_baik' => ['count' => $stats['sangat_baik'], 'percentage' => $total ? round($stats['sangat_baik'] / $total * 100) : 0],
            'baik' => ['count' => $stats['baik'], 'percentage' => $total ? round($stats['baik'] / $total * 100) : 0],
            'cukup' => ['count' => $stats['cukup'], 'percentage' => $total ? round($stats['cukup'] / $total * 100) : 0],
            'kurang' => ['count' => $stats['kurang'], 'percentage' => $total ? round($stats['kurang'] / $total * 100) : 0],
        ];
    }
}
