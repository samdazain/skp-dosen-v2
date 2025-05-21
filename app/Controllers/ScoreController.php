<?php

namespace App\Controllers;

class ScoreController extends BaseController
{
    /**
     * Display the score management interface
     */
    public function index()
    {
        // Ensure user is logged in and is an admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // if (session()->get('user_role') !== 'admin') {
        //     return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        // }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        // In a real application, you would load data from the database
        // For now, we'll just use dummy data
        $scoreRanges = $this->getDummyScoreRanges();

        return view('score/index', [
            'pageTitle' => 'Setting Nilai | SKP Dosen',
            'user' => $userData,
            'scoreRanges' => $scoreRanges
        ]);
    }

    /**
     * Update score range data
     */
    public function updateScoreRanges()
    {
        // Ensure user is logged in and is an admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        }

        // Get POST data
        $category = $this->request->getPost('category');
        $subcategory = $this->request->getPost('subcategory');
        $ranges = $this->request->getPost('ranges');

        // In a real application, you would validate and update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('score')->with('success', 'Rentang nilai berhasil diperbarui');
    }

    /**
     * Add a new score range
     */
    public function addScoreRange()
    {
        // Ensure user is logged in and is an admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        }

        // Get POST data
        $category = $this->request->getPost('category');
        $subcategory = $this->request->getPost('subcategory');
        $rangeStart = $this->request->getPost('range_start');
        $rangeEnd = $this->request->getPost('range_end');
        $score = $this->request->getPost('score');

        // In a real application, you would validate and update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('score')->with('success', 'Rentang nilai baru berhasil ditambahkan');
    }

    /**
     * Delete a score range
     */
    public function deleteScoreRange()
    {
        // Ensure user is logged in and is an admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini');
        }

        // Get POST data
        $rangeId = $this->request->getPost('range_id');

        // In a real application, you would delete from the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('score')->with('success', 'Rentang nilai berhasil dihapus');
    }

    /**
     * Get dummy score range data for demonstration
     */
    private function getDummyScoreRanges()
    {
        return [
            'integrity' => [
                'title' => 'Data Integritas',
                'subcategories' => [
                    'teaching_attendance' => [
                        'title' => 'Kehadiran Mengajar',
                        'ranges' => [
                            ['id' => 1, 'start' => 0, 'end' => 4, 'score' => 60, 'label' => '0-4'],
                            ['id' => 2, 'start' => 5, 'end' => 8, 'score' => 75, 'label' => '5-8'],
                            ['id' => 3, 'start' => 8, 'end' => 10, 'score' => 85, 'label' => '8-10'],
                            ['id' => 4, 'start' => 10, 'end' => null, 'score' => 88, 'label' => '>10'],
                        ]
                    ],
                    'courses_taught' => [
                        'title' => 'Jumlah MK di Ampu',
                        'ranges' => [
                            ['id' => 5, 'start' => 1, 'end' => 2, 'score' => 75, 'label' => '1-2'],
                            ['id' => 6, 'start' => 3, 'end' => 4, 'score' => 80, 'label' => '3-4'],
                            ['id' => 7, 'start' => 5, 'end' => 6, 'score' => 85, 'label' => '5-6'],
                            ['id' => 8, 'start' => 6, 'end' => null, 'score' => 88, 'label' => '>6'],
                        ]
                    ]
                ]
            ],
            'discipline' => [
                'title' => 'Data Disiplin',
                'subcategories' => [
                    'daily_attendance' => [
                        'title' => 'Presensi Harian (jumlah alpha)',
                        'ranges' => [
                            ['id' => 9, 'start' => 1, 'end' => 2, 'score' => 85, 'label' => '1-2'],
                            ['id' => 10, 'start' => 3, 'end' => 4, 'score' => 80, 'label' => '3-4'],
                            ['id' => 11, 'start' => 5, 'end' => 6, 'score' => 75, 'label' => '5-6'],
                            ['id' => 12, 'start' => 6, 'end' => null, 'score' => 60, 'label' => '>6'],
                        ]
                    ],
                    'morning_exercise' => [
                        'title' => 'Presensi Senam Pagi (jumlah alpha)',
                        'ranges' => [
                            ['id' => 13, 'start' => 0, 'end' => 0, 'score' => 88, 'label' => '0'],
                            ['id' => 14, 'start' => 1, 'end' => 2, 'score' => 85, 'label' => '1-2'],
                            ['id' => 15, 'start' => 3, 'end' => 4, 'score' => 80, 'label' => '3-4'],
                            ['id' => 16, 'start' => 5, 'end' => 6, 'score' => 75, 'label' => '5-6'],
                            ['id' => 17, 'start' => 7, 'end' => 8, 'score' => 70, 'label' => '7-8'],
                            ['id' => 18, 'start' => 8, 'end' => null, 'score' => 60, 'label' => '>8 atau 3 kali berturut alpha'],
                        ]
                    ],
                    'ceremony_attendance' => [
                        'title' => 'Presensi Upacara (jumlah alpha)',
                        'ranges' => [
                            ['id' => 19, 'start' => 0, 'end' => 0, 'score' => 88, 'label' => '0'],
                            ['id' => 20, 'start' => 1, 'end' => 2, 'score' => 80, 'label' => '1-2'],
                            ['id' => 21, 'start' => 3, 'end' => 4, 'score' => 70, 'label' => '3-4'],
                            ['id' => 22, 'start' => 4, 'end' => null, 'score' => 60, 'label' => '>4'],
                        ]
                    ]
                ]
            ],
            'commitment' => [
                'title' => 'Data Komitmen',
                'subcategories' => [
                    'competency' => [
                        'title' => 'Kompetensi (aktif)',
                        'ranges' => [
                            ['id' => 23, 'start' => null, 'end' => null, 'score' => 88, 'label' => 'Ada', 'type' => 'boolean', 'value' => true],
                            ['id' => 24, 'start' => null, 'end' => null, 'score' => 70, 'label' => 'Tidak', 'type' => 'boolean', 'value' => false],
                        ]
                    ],
                    'tri_dharma' => [
                        'title' => 'Tri Dharma (BKD)',
                        'ranges' => [
                            ['id' => 25, 'start' => null, 'end' => null, 'score' => 88, 'label' => 'Lulus', 'type' => 'boolean', 'value' => true],
                            ['id' => 26, 'start' => null, 'end' => null, 'score' => 70, 'label' => 'Tidak Lulus', 'type' => 'boolean', 'value' => false],
                        ]
                    ]
                ]
            ],
            'cooperation' => [
                'title' => 'Kerjasama (Koprodi / Dekanat)',
                'subcategories' => [
                    'cooperation_level' => [
                        'title' => 'Tingkat Kerjasama',
                        'ranges' => [
                            ['id' => 27, 'start' => null, 'end' => null, 'score' => 60, 'label' => 'Tidak Kooperatif', 'type' => 'fixed'],
                            ['id' => 28, 'start' => null, 'end' => null, 'score' => 75, 'label' => 'Cukup Kooperatif', 'type' => 'fixed'],
                            ['id' => 29, 'start' => null, 'end' => null, 'score' => 80, 'label' => 'Kooperatif', 'type' => 'fixed'],
                            ['id' => 30, 'start' => null, 'end' => null, 'score' => 88, 'label' => 'Sangat Kooperatif', 'type' => 'fixed'],
                        ]
                    ]
                ]
            ],
            'orientation' => [
                'title' => 'Orientasi Pelayanan',
                'subcategories' => [
                    'teaching_questionnaire' => [
                        'title' => 'Kuisioner Mengajar',
                        'ranges' => [
                            ['id' => 31, 'start' => 3.5, 'end' => null, 'score' => 88, 'label' => '>3.5'],
                            ['id' => 32, 'start' => 3, 'end' => 3.5, 'score' => 85, 'label' => '3 - 3.5'],
                            ['id' => 33, 'start' => 2.75, 'end' => 3, 'score' => 80, 'label' => '2.75 - 3'],
                            ['id' => 34, 'start' => 2.5, 'end' => 2.75, 'score' => 70, 'label' => '2.5 - 2.75'],
                            ['id' => 35, 'start' => null, 'end' => 2.5, 'score' => 60, 'label' => '<2.5'],
                        ]
                    ]
                ]
            ]
        ];
    }
}
