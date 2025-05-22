<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Integritas Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Integritas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Integritas Dosen Fakultas</h3>
                        <div class="card-tools">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('integrity/export-excel') ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('integrity/export-pdf') ?>" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                </a>
                            </div>
                            <div class="input-group input-group-sm ml-3 pt-1 float-right" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" id="searchInput"
                                    placeholder="Cari berdasarkan nama...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Dosen</th>
                                    <th class="text-center">Kehadiran Mengajar</th>
                                    <th class="text-center">Jumlah Mata Kuliah</th>
                                    <th class="text-center">Nilai Rata-rata</th>
                                    <th class="text-center">Terakhir Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dummy data untuk demonstrasi
                                $dummyData = [
                                    [
                                        'name' => 'Dr. Budi Santoso, M.Kom',
                                        'attendance' => 90,
                                        'courses' => 3,
                                        'updated_at' => '2025-05-15 08:30:00'
                                    ],
                                    [
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'attendance' => 85,
                                        'courses' => 4,
                                        'updated_at' => '2025-05-14 14:25:00'
                                    ],
                                    [
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'attendance' => 95,
                                        'courses' => 2,
                                        'updated_at' => '2025-05-18 09:15:00'
                                    ],
                                    [
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'attendance' => 80,
                                        'courses' => 4,
                                        'updated_at' => '2025-05-17 10:45:00'
                                    ],
                                    [
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'attendance' => 88,
                                        'courses' => 3,
                                        'updated_at' => '2025-05-16 13:20:00'
                                    ],
                                    [
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'attendance' => 92,
                                        'courses' => 3,
                                        'updated_at' => '2025-05-15 15:10:00'
                                    ],
                                    [
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'attendance' => 87,
                                        'courses' => 2,
                                        'updated_at' => '2025-05-14 11:05:00'
                                    ]
                                ];

                                // Calculate average score
                                function calculateAverageScore($attendance, $courses)
                                {
                                    return round(($attendance + ($courses * 25)) / 2, 1);
                                }

                                // Format date
                                function formatDate($dateString)
                                {
                                    $date = new \DateTime($dateString);
                                    return $date->format('d M Y H:i');
                                }

                                // Get score class based on value
                                function getScoreClass($score)
                                {
                                    if ($score >= 90) return 'text-success font-weight-bold';
                                    if ($score >= 75) return 'text-primary';
                                    if ($score >= 60) return 'text-warning';
                                    return 'text-danger';
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    $averageScore = calculateAverageScore($lecturer['attendance'], $lecturer['courses']);
                                    $scoreClass = getScoreClass($averageScore);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td class="text-center"><?= $lecturer['attendance'] ?>%</td>
                                        <td class="text-center"><?= $lecturer['courses'] ?></td>
                                        <td class="text-center"><span class="<?= $scoreClass ?>"><?= $averageScore ?></span>
                                        </td>
                                        <td class="text-center"><?= formatDate($lecturer['updated_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Integritas Dosen</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata Kehadiran</span>
                                                <span class="info-box-number">88.1%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Mata Kuliah</span>
                                                <span class="info-box-number">21</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-group mt-4">
                                    <span class="progress-text">Rata-rata Nilai Integritas</span>
                                    <span class="float-right"><b>85.7</b>/100</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 85.7%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Distribusi Nilai</h3>
                            </div>
                            <div class="card-body">
                                <div class="progress-group">
                                    <span class="progress-text">Sangat Baik (90-100)</span>
                                    <span class="float-right"><b>2</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 28.6%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Baik (75-89)</span>
                                    <span class="float-right"><b>5</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 71.4%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Cukup (60-74)</span>
                                    <span class="float-right"><b>0</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Kurang (<60)< /span>
                                            <span class="float-right"><b>0</b>/7 dosen</span>
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" style="width: 0%"></div>
                                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();

                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>