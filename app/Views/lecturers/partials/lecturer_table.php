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

<div class="card shadow">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="card-title mb-0">
                    <i class="fas fa-users mr-2"></i>
                    Data Dosen Fakultas
                </h3>
            </div>
            <div class="col-md-6">
                <div class="card-tools float-right">
                    <a href="<?= base_url('lecturers/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Dosen
                    </a>
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download mr-1"></i>
                            Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= base_url('lecturers/export-excel' . ($search ? '?search=' . urlencode($search) : '')) ?>">
                                <i class="fas fa-file-excel mr-2"></i>
                                Excel
                            </a>
                            <a class="dropdown-item" href="<?= base_url('lecturers/export-pdf' . ($search ? '?search=' . urlencode($search) : '')) ?>">
                                <i class="fas fa-file-pdf mr-2"></i>
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row mt-3">
            <div class="col-md-6">
                <form id="searchForm" method="GET" action="<?= base_url('lecturers') ?>">
                    <div class="input-group">
                        <input type="text"
                            id="searchInput"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama, NIP, jabatan, atau program studi..."
                            value="<?= esc($search) ?>">
                        <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
                        <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">
                        <input type="hidden" name="per_page" value="<?= esc($perPage) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if ($search): ?>
                                <a href="<?= base_url('lecturers') ?>" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="float-right">
                    <small class="text-muted">
                        Total: <?= number_format($total) ?> dosen
                        <?php if ($search): ?>
                            | Hasil pencarian: "<?= esc($search) ?>"
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped" id="lecturerTable">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th class="sortable" data-sort="name" style="min-width: 200px;">
                        Nama Dosen
                    </th>
                    <th class="sortable" data-sort="nip" style="width: 150px;">
                        NIP
                    </th>
                    <th class="sortable" data-sort="position" style="min-width: 200px;">
                        Jabatan
                    </th>
                    <th class="sortable" data-sort="study_program" style="width: 180px;">
                        Program Studi
                    </th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lecturers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p class="mb-0">
                                    <?php if ($search): ?>
                                        Tidak ada hasil untuk pencarian "<?= esc($search) ?>"
                                    <?php else: ?>
                                        Belum ada data dosen
                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lecturers as $index => $lecturer): ?>
                        <tr data-position="<?= esc($lecturer['position']) ?>"
                            data-study-program="<?= esc($lecturer['study_program'] ?? '') ?>">
                            <td class="text-center">
                                <span class="font-weight-bold">
                                    <?= (($pagination['currentPage'] - 1) * $pagination['perPage']) + $index + 1 ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="lecturer-avatar mr-3">
                                        <div class="avatar-circle bg-primary text-white">
                                            <?= strtoupper(substr($lecturer['name'], 0, 2)) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <strong class="d-block"><?= esc($lecturer['name']) ?></strong>
                                        <small class="text-muted">NIP: <?= esc($lecturer['nip']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light text-dark px-2 py-1 rounded">
                                    <?= esc($lecturer['nip']) ?>
                                </code>
                            </td>
                            <td>
                                <span class="badge badge-info badge-pill">
                                    <?= esc($lecturer['position']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($lecturer['study_program'])): ?>
                                    <span class="badge badge-secondary badge-pill">
                                        <?= esc($lecturer['study_program']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('lecturers/edit/' . $lecturer['id']) ?>"
                                        class="btn btn-sm btn-warning"
                                        data-toggle="tooltip"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        data-toggle="tooltip"
                                        title="Hapus"
                                        onclick="confirmDelete(<?= $lecturer['id'] ?>, '<?= esc($lecturer['name']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pagination) && $pagination['hasPages']): ?>
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Menampilkan <?= $pagination['startRecord'] ?> - <?= $pagination['endRecord'] ?>
                        dari <?= number_format($pagination['total']) ?> data
                    </small>
                </div>
                <div class="col-md-6">
                    <?= view('components/pagination', ['pagination' => $pagination]) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .lecturer-avatar {
        flex-shrink: 0;
    }
</style>