<?php

/**
 * @var CodeIgniter\View\View $this
 */

// Define lecturer table columns
$columns = [
    [
        'field' => 'number',
        'label' => 'No',
        'class' => 'text-center col-no',
        'sortable' => false,
        'render' => function ($row, $index) {
            return "<span class='font-weight-bold'>{$index}</span>";
        }
    ],
    [
        'field' => 'name',
        'label' => 'Nama Dosen',
        'class' => 'col-name',
        'sortable' => true,
        'render' => function ($row, $index) {
            return view('lecturers/partials/name_cell', ['lecturer' => $row]);
        }
    ],
    [
        'field' => 'nip',
        'label' => 'NIP',
        'class' => 'col-nip',
        'sortable' => true,
        'render' => function ($row, $index) {
            return "<code class='bg-light text-dark px-2 py-1 rounded d-inline-block text-truncate' style='max-width: 100%;'>" . esc($row['nip']) . "</code>";
        }
    ],
    [
        'field' => 'position',
        'label' => 'Jabatan',
        'class' => 'col-position',
        'sortable' => true,
        'render' => function ($row, $index) {
            return view('lecturers/partials/position_badge', ['position' => $row['position']]);
        }
    ],
    [
        'field' => 'study_program',
        'label' => 'Program Studi',
        'class' => 'text-center col-program',
        'sortable' => true,
        'render' => function ($row, $index) {
            return view('lecturers/partials/program_badge', ['program' => $row['study_program'] ?? null]);
        }
    ],
    [
        'field' => 'actions',
        'label' => 'Aksi',
        'class' => 'text-center col-actions',
        'sortable' => false,
        'render' => function ($row, $index) {
            return view('lecturers/partials/action_buttons', ['lecturer' => $row]);
        }
    ]
];

// Configure search
$searchConfig = [
    'searchUrl' => base_url('lecturers'),
    'searchTerm' => $search ?? '',
    'placeholder' => 'Cari nama atau NIP dosen...',
    'hiddenFields' => [
        'sort_by' => $sortBy ?? 'name',
        'sort_order' => $sortOrder ?? 'asc',
        'per_page' => $perPage ?? 10
    ],
    'showResults' => true
];

// Configure exports
$exportConfig = [
    'exports' => [
        'excel' => [
            'url' => base_url('lecturers/export-excel'),
            'label' => 'Excel'
        ],
        'pdf' => [
            'url' => base_url('lecturers/export-pdf'),
            'label' => 'PDF'
        ]
    ]
];
?>

<?= view('Components/data_table', [
    'title' => 'Data Dosen Fakultas',
    'icon' => 'fas fa-users',
    'data' => $lecturers,
    'columns' => $columns,
    'searchConfig' => $searchConfig,
    'exportConfig' => $exportConfig,
    'pagination' => $pagination ?? [],
    'addUrl' => base_url('lecturers/create'),
    'addLabel' => 'Tambah Dosen',
    'emptyMessage' => 'Tidak ada data dosen',
    'cssFile' => 'assets/css/lecturer_table.css',
    'jsFile' => 'assets/js/lecturer_table.js',
    // Pass current sorting parameters
    'sortBy' => $sortBy ?? 'name',
    'sortOrder' => $sortOrder ?? 'asc',
    'perPage' => $perPage ?? 10
]) ?>