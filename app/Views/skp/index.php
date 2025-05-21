<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Master SKP</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Master SKP</li>
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
                        <h3 class="card-title">Daftar Penilaian Kinerja Dosen</h3>
                        <div class="card-tools d-flex align-items-center">
                            <a href="<?= base_url('skp/export-excel') ?>" class="btn btn-sm btn-success mr-1">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </a>
                            <a href="<?= base_url('skp/export-pdf') ?>" class="btn btn-sm btn-danger mr-2">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF
                            </a>
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" id="searchInput"
                                    placeholder="Cari...">
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
                                    <th>NIP</th>
                                    <th class="text-center">Program Studi</th>
                                    <th class="text-center">Integritas</th>
                                    <th class="text-center">Disiplin</th>
                                    <th class="text-center">Komitmen</th>
                                    <th class="text-center">Kerjasama</th>
                                    <th class="text-center">Orientasi<br>Pelayanan</th>
                                    <th class="text-center">Nilai Total</th>
                                    <th class="text-center">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dummy data untuk demonstrasi
                                $dummyData = [
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
                                    [
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'nip' => '198003212005012002',
                                        'study_program' => 'Sistem Informasi',
                                        'integrity' => 88,
                                        'discipline' => 90,
                                        'commitment' => 85,
                                        'cooperation' => 87,
                                        'service' => 91
                                    ],
                                    [
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'nip' => '196812101995121001',
                                        'study_program' => 'Magister Teknologi Informasi',
                                        'integrity' => 95,
                                        'discipline' => 92,
                                        'commitment' => 90,
                                        'cooperation' => 93,
                                        'service' => 94
                                    ],
                                    [
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'nip' => '198907182015042003',
                                        'study_program' => 'Sains Data',
                                        'integrity' => 82,
                                        'discipline' => 80,
                                        'commitment' => 85,
                                        'cooperation' => 88,
                                        'service' => 84
                                    ],
                                    [
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'nip' => '197303052001121003',
                                        'study_program' => 'Bisnis Digital',
                                        'integrity' => 87,
                                        'discipline' => 85,
                                        'commitment' => 86,
                                        'cooperation' => 89,
                                        'service' => 88
                                    ],
                                    [
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'nip' => '198505122010122005',
                                        'study_program' => 'Informatika',
                                        'integrity' => 89,
                                        'discipline' => 91,
                                        'commitment' => 88,
                                        'cooperation' => 90,
                                        'service' => 92
                                    ],
                                    [
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'nip' => '197202182000031001',
                                        'study_program' => 'Sistem Informasi',
                                        'integrity' => 86,
                                        'discipline' => 84,
                                        'commitment' => 88,
                                        'cooperation' => 85,
                                        'service' => 87
                                    ]
                                ];

                                function calculateTotal($scores)
                                {
                                    return round(($scores['integrity'] + $scores['discipline'] + $scores['commitment'] +
                                        $scores['cooperation'] + $scores['service']) / 5, 1);
                                }

                                function getCategory($total)
                                {
                                    if ($total >= 91) return ['Sangat Baik', 'success'];
                                    if ($total >= 76) return ['Baik', 'primary'];
                                    if ($total >= 61) return ['Cukup', 'warning'];
                                    if ($total >= 51) return ['Kurang', 'danger'];
                                    return ['Buruk', 'dark'];
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    $total = calculateTotal($lecturer);
                                    list($category, $badgeColor) = getCategory($total);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td><?= $lecturer['nip'] ?></td>
                                        <td class="text-center"><?= $lecturer['study_program'] ?></td>
                                        <td class="text-center"><?= $lecturer['integrity'] ?></td>
                                        <td class="text-center"><?= $lecturer['discipline'] ?></td>
                                        <td class="text-center"><?= $lecturer['commitment'] ?></td>
                                        <td class="text-center"><?= $lecturer['cooperation'] ?></td>
                                        <td class="text-center"><?= $lecturer['service'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $badgeColor ?> px-3 py-2"><?= $total ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $badgeColor ?>"><?= $category ?></span>
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

                <!-- Summary Cards -->
                <div class="row mt-4">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-thumbs-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sangat Baik</span>
                                <span class="info-box-number">1</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 14%"></div>
                                </div>
                                <span class="progress-description">14% dari total dosen</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-primary">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Baik</span>
                                <span class="info-box-number">6</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 86%"></div>
                                </div>
                                <span class="progress-description">86% dari total dosen</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon"><i class="fas fa-exclamation"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cukup</span>
                                <span class="info-box-number">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">0% dari total dosen</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box bg-gradient-danger">
                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Kurang</span>
                                <span class="info-box-number">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">0% dari total dosen</span>
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
                const nip = row.cells[2].textContent.toLowerCase();
                const program = row.cells[3].textContent.toLowerCase();

                if (name.includes(searchTerm) || nip.includes(searchTerm) || program.includes(
                        searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>