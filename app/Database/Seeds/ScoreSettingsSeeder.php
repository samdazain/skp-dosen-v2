<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ScoreSettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Data Integritas - INTEGER VALUES
            [
                'category' => 'integrity',
                'subcategory' => 'teaching_attendance',
                'range_type' => 'range',
                'range_start' => 0,
                'range_end' => 4,
                'range_label' => '0-4',
                'score' => 60,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'teaching_attendance',
                'range_type' => 'range',
                'range_start' => 5,
                'range_end' => 7,
                'range_label' => '5-7',
                'score' => 75,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'teaching_attendance',
                'range_type' => 'range',
                'range_start' => 8,
                'range_end' => 10,
                'range_label' => '8-10',
                'score' => 85,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'teaching_attendance',
                'range_type' => 'above',
                'range_start' => 10,
                'range_end' => null,
                'range_label' => '>10',
                'score' => 88,
                'editable' => true
            ],

            [
                'category' => 'integrity',
                'subcategory' => 'courses_taught',
                'range_type' => 'range',
                'range_start' => 1,
                'range_end' => 2,
                'range_label' => '1-2',
                'score' => 75,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'courses_taught',
                'range_type' => 'range',
                'range_start' => 3,
                'range_end' => 4,
                'range_label' => '3-4',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'courses_taught',
                'range_type' => 'range',
                'range_start' => 5,
                'range_end' => 6,
                'range_label' => '5-6',
                'score' => 85,
                'editable' => true
            ],
            [
                'category' => 'integrity',
                'subcategory' => 'courses_taught',
                'range_type' => 'above',
                'range_start' => 6,
                'range_end' => null,
                'range_label' => '>6',
                'score' => 88,
                'editable' => true
            ],

            // Data Disiplin - INTEGER VALUES
            [
                'category' => 'discipline',
                'subcategory' => 'daily_attendance',
                'range_type' => 'exact',
                'range_start' => 0,
                'range_end' => null,
                'range_label' => '0',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'daily_attendance',
                'range_type' => 'range',
                'range_start' => 1,
                'range_end' => 2,
                'range_label' => '1-2',
                'score' => 85,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'daily_attendance',
                'range_type' => 'range',
                'range_start' => 3,
                'range_end' => 4,
                'range_label' => '3-4',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'daily_attendance',
                'range_type' => 'range',
                'range_start' => 5,
                'range_end' => 6,
                'range_label' => '5-6',
                'score' => 75,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'daily_attendance',
                'range_type' => 'above',
                'range_start' => 6,
                'range_end' => null,
                'range_label' => '>6',
                'score' => 60,
                'editable' => true
            ],

            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'exact',
                'range_start' => 0,
                'range_end' => null,
                'range_label' => '0',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'range',
                'range_start' => 1,
                'range_end' => 2,
                'range_label' => '1-2',
                'score' => 85,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'range',
                'range_start' => 3,
                'range_end' => 4,
                'range_label' => '3-4',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'range',
                'range_start' => 5,
                'range_end' => 6,
                'range_label' => '5-6',
                'score' => 75,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'range',
                'range_start' => 7,
                'range_end' => 8,
                'range_label' => '7-8',
                'score' => 70,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'morning_exercise',
                'range_type' => 'above',
                'range_start' => 8,
                'range_end' => null,
                'range_label' => '>8 atau 3 kali berturut alpha',
                'score' => 60,
                'editable' => true
            ],

            [
                'category' => 'discipline',
                'subcategory' => 'ceremony_attendance',
                'range_type' => 'exact',
                'range_start' => 0,
                'range_end' => null,
                'range_label' => '0',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'ceremony_attendance',
                'range_type' => 'range',
                'range_start' => 1,
                'range_end' => 2,
                'range_label' => '1-2',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'ceremony_attendance',
                'range_type' => 'range',
                'range_start' => 3,
                'range_end' => 4,
                'range_label' => '3-4',
                'score' => 70,
                'editable' => true
            ],
            [
                'category' => 'discipline',
                'subcategory' => 'ceremony_attendance',
                'range_type' => 'above',
                'range_start' => 4,
                'range_end' => null,
                'range_label' => '>4',
                'score' => 60,
                'editable' => true
            ],

            // Komitmen - BOOLEAN VALUES
            [
                'category' => 'commitment',
                'subcategory' => 'competency',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Ada',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'commitment',
                'subcategory' => 'competency',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Tidak',
                'score' => 70,
                'editable' => true
            ],

            [
                'category' => 'commitment',
                'subcategory' => 'tri_dharma',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Lulus',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'commitment',
                'subcategory' => 'tri_dharma',
                'range_type' => 'boolean',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Tidak Lulus',
                'score' => 70,
                'editable' => true
            ],

            // Kerjasama - FIXED VALUES
            [
                'category' => 'cooperation',
                'subcategory' => 'cooperation_level',
                'range_type' => 'fixed',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Tidak Kooperatif',
                'score' => 60,
                'editable' => true
            ],
            [
                'category' => 'cooperation',
                'subcategory' => 'cooperation_level',
                'range_type' => 'fixed',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Cukup Kooperatif',
                'score' => 75,
                'editable' => true
            ],
            [
                'category' => 'cooperation',
                'subcategory' => 'cooperation_level',
                'range_type' => 'fixed',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Kooperatif',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'cooperation',
                'subcategory' => 'cooperation_level',
                'range_type' => 'fixed',
                'range_start' => null,
                'range_end' => null,
                'range_label' => 'Sangat Kooperatif',
                'score' => 88,
                'editable' => true
            ],

            // Orientasi Pelayanan - DECIMAL VALUES
            [
                'category' => 'orientation',
                'subcategory' => 'teaching_questionnaire',
                'range_type' => 'above',
                'range_start' => 3.5,
                'range_end' => null,
                'range_label' => '>3.5',
                'score' => 88,
                'editable' => true
            ],
            [
                'category' => 'orientation',
                'subcategory' => 'teaching_questionnaire',
                'range_type' => 'range',
                'range_start' => 3.0,
                'range_end' => 3.5,
                'range_label' => '3.0 - 3.5',
                'score' => 85,
                'editable' => true
            ],
            [
                'category' => 'orientation',
                'subcategory' => 'teaching_questionnaire',
                'range_type' => 'range',
                'range_start' => 2.75,
                'range_end' => 3.0,
                'range_label' => '2.75 - 3.0',
                'score' => 80,
                'editable' => true
            ],
            [
                'category' => 'orientation',
                'subcategory' => 'teaching_questionnaire',
                'range_type' => 'range',
                'range_start' => 2.5,
                'range_end' => 2.75,
                'range_label' => '2.5 - 2.75',
                'score' => 70,
                'editable' => true
            ],
            [
                'category' => 'orientation',
                'subcategory' => 'teaching_questionnaire',
                'range_type' => 'below',
                'range_start' => null,
                'range_end' => 2.5,
                'range_label' => '<2.5',
                'score' => 60,
                'editable' => true
            ]
        ];

        $this->db->table('score_settings')->insertBatch($data);
    }
}
