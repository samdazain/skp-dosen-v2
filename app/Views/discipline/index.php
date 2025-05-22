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
                <h1 class="m-0">Data Disiplin Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Disiplin</li>
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
                        <h3 class="card-title">Data Disiplin Dosen Fakultas</h3>
                        <div class="card-tools">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('discipline/export-excel') ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('discipline/export-pdf') ?>" class="btn btn-sm btn-danger">
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
                                    <th class="text-center">Kehadiran Harian</th>
                                    <th class="text-center">Kehadiran Senam Pagi</th>
                                    <th class="text-center">Kehadiran Upacara</th>
                                    <th class="text-center">Nilai Rata-rata</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dummy data untuk demonstrasi
                                $dummyData = [
                                    [
                                        'name' => 'Dr. Budi Santoso, M.Kom',
                                        'daily_attendance' => 92,
                                        'exercise_attendance' => 85,
                                        'ceremony_attendance' => 100,
                                    ],
                                    [
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'daily_attendance' => 96,
                                        'exercise_attendance' => 90,
                                        'ceremony_attendance' => 100,
                                    ],
                                    [
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'daily_attendance' => 98,
                                        'exercise_attendance' => 75,
                                        'ceremony_attendance' => 100,
                                    ],
                                    [
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'daily_attendance' => 88,
                                        'exercise_attendance' => 80,
                                        'ceremony_attendance' => 85,
                                    ],
                                    [
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'daily_attendance' => 90,
                                        'exercise_attendance' => 70,
                                        'ceremony_attendance' => 100,
                                    ],
                                    [
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'daily_attendance' => 95,
                                        'exercise_attendance' => 80,
                                        'ceremony_attendance' => 100,
                                    ],
                                    [
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'daily_attendance' => 92,
                                        'exercise_attendance' => 65,
                                        'ceremony_attendance' => 100,
                                    ]
                                ];

                                // Calculate average score
                                function calculateAverageScore($daily, $exercise, $ceremony)
                                {
                                    // Weighted average: daily (60%), exercise (20%), ceremony (20%)
                                    return round(($daily * 0.6) + ($exercise * 0.2) + ($ceremony * 0.2), 1);
                                }

                                // Get score class and label based on value
                                function getScoreInfo($score)
                                {
                                    if ($score >= 90) return ['text-success', 'Sangat Baik'];
                                    if ($score >= 80) return ['text-primary', 'Baik'];
                                    if ($score >= 70) return ['text-warning', 'Cukup'];
                                    return ['text-danger', 'Kurang'];
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    $averageScore = calculateAverageScore(
                                        $lecturer['daily_attendance'],
                                        $lecturer['exercise_attendance'],
                                        $lecturer['ceremony_attendance']
                                    );
                                    list($scoreClass, $scoreLabel) = getScoreInfo($averageScore);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td class="text-center"><?= $lecturer['daily_attendance'] ?>%</td>
                                        <td class="text-center"><?= $lecturer['exercise_attendance'] ?>%</td>
                                        <td class="text-center"><?= $lecturer['ceremony_attendance'] ?>%</td>
                                        <td class="text-center font-weight-bold <?= $scoreClass ?>"><?= $averageScore ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= str_replace('text-', '', $scoreClass) ?>">
                                                <?= $scoreLabel ?>
                                            </span>
                                        </td>
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

                <!-- Summary Cards and Charts -->
                <div class="row mt-4">
                    <!-- Daily Attendance Stats -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    Kehadiran Harian
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h3 class="text-primary">93.0%</h3>
                                    <p class="mb-0">Rata-rata Kehadiran</p>
                                </div>
                                <div class="progress-group mt-3">
                                    <span class="progress-text">≥ 95%</span>
                                    <span class="float-right"><b>3</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 42.9%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">90% - 94%</span>
                                    <span class="float-right"><b>3</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 42.9%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">80% - 89%</span>
                                    <span class="float-right"><b>1</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 14.2%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exercise Attendance Stats -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-running mr-1"></i>
                                    Kehadiran Senam
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h3 class="text-primary">77.9%</h3>
                                    <p class="mb-0">Rata-rata Kehadiran</p>
                                </div>
                                <div class="progress-group mt-3">
                                    <span class="progress-text">≥ 90%</span>
                                    <span class="float-right"><b>1</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 14.3%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">80% - 89%</span>
                                    <span class="float-right"><b>3</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 42.8%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">65% - 79%</span>
                                    <span class="float-right"><b>3</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 42.9%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ceremony Attendance Stats -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-flag mr-1"></i>
                                    Kehadiran Upacara
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h3 class="text-primary">97.9%</h3>
                                    <p class="mb-0">Rata-rata Kehadiran</p>
                                </div>
                                <div class="progress-group mt-3">
                                    <span class="progress-text">100%</span>
                                    <span class="float-right"><b>6</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 85.7%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">85% - 99%</span>
                                    <span class="float-right"><b>1</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 14.3%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">
                                        < 85%</span>
                                            <span class="float-right"><b>0</b>/7 dosen</span>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" style="width: 0%"></div>
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