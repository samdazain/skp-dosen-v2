<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Kerja Sama Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Kerja Sama</li>
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
                        <h3 class="card-title">Data Kerja Sama Dosen Fakultas</h3>
                        <div class="card-tools">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('cooperation/export-excel') ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('cooperation/export-pdf') ?>" class="btn btn-sm btn-danger">
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
                                    <th class="text-center">Tingkat Kerja Sama</th>
                                    <th class="text-center">Nilai Total</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dummy data untuk demonstrasi
                                $dummyData = [
                                    [
                                        'id' => 1,
                                        'name' => 'Dr. Budi Santoso, M.Kom',
                                        'cooperation_level' => 90 // Sangat Kooperatif
                                    ],
                                    [
                                        'id' => 2,
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'cooperation_level' => 80 // Kooperatif
                                    ],
                                    [
                                        'id' => 3,
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'cooperation_level' => 90 // Sangat Kooperatif
                                    ],
                                    [
                                        'id' => 4,
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'cooperation_level' => 75 // Cukup Kooperatif
                                    ],
                                    [
                                        'id' => 5,
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'cooperation_level' => 60 // Tidak Kooperatif
                                    ],
                                    [
                                        'id' => 6,
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'cooperation_level' => 80 // Kooperatif
                                    ],
                                    [
                                        'id' => 7,
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'cooperation_level' => 75 // Cukup Kooperatif
                                    ]
                                ];

                                // Get score status based on value
                                function getScoreStatus($score)
                                {
                                    if ($score >= 90) return ['text-success', 'badge-success', 'Sangat Baik'];
                                    if ($score >= 80) return ['text-primary', 'badge-primary', 'Baik'];
                                    if ($score >= 70) return ['text-info', 'badge-info', 'Cukup'];
                                    return ['text-danger', 'badge-danger', 'Kurang'];
                                }

                                // Get cooperation level text
                                function getCooperationLevelText($level)
                                {
                                    switch ($level) {
                                        case 90:
                                            return 'Sangat Kooperatif';
                                        case 80:
                                            return 'Kooperatif';
                                        case 75:
                                            return 'Cukup Kooperatif';
                                        case 60:
                                            return 'Tidak Kooperatif';
                                        default:
                                            return 'Tidak Diketahui';
                                    }
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    list($scoreClass, $badgeClass, $statusLabel) = getScoreStatus($lecturer['cooperation_level']);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label
                                                    class="btn btn-xs btn-outline-danger <?= $lecturer['cooperation_level'] == 60 ? 'active' : '' ?>">
                                                    <input type="radio" name="cooperation_<?= $lecturer['id'] ?>" value="60"
                                                        <?= $lecturer['cooperation_level'] == 60 ? 'checked' : '' ?>
                                                        onclick="confirmCooperationChange(<?= $lecturer['id'] ?>, 60)">
                                                    Tidak
                                                </label>
                                                <label
                                                    class="btn btn-xs btn-outline-info <?= $lecturer['cooperation_level'] == 75 ? 'active' : '' ?>">
                                                    <input type="radio" name="cooperation_<?= $lecturer['id'] ?>" value="75"
                                                        <?= $lecturer['cooperation_level'] == 75 ? 'checked' : '' ?>
                                                        onclick="confirmCooperationChange(<?= $lecturer['id'] ?>, 75)">
                                                    Cukup
                                                </label>
                                                <label
                                                    class="btn btn-xs btn-outline-primary <?= $lecturer['cooperation_level'] == 80 ? 'active' : '' ?>">
                                                    <input type="radio" name="cooperation_<?= $lecturer['id'] ?>" value="80"
                                                        <?= $lecturer['cooperation_level'] == 80 ? 'checked' : '' ?>
                                                        onclick="confirmCooperationChange(<?= $lecturer['id'] ?>, 80)">
                                                    Baik
                                                </label>
                                                <label
                                                    class="btn btn-xs btn-outline-success <?= $lecturer['cooperation_level'] == 90 ? 'active' : '' ?>">
                                                    <input type="radio" name="cooperation_<?= $lecturer['id'] ?>" value="90"
                                                        <?= $lecturer['cooperation_level'] == 90 ? 'checked' : '' ?>
                                                        onclick="confirmCooperationChange(<?= $lecturer['id'] ?>, 90)">
                                                    Sangat
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold <?= $scoreClass ?>">
                                            <?= $lecturer['cooperation_level'] ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
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
                    <!-- Cooperation Level Distribution -->
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Distribusi Tingkat Kerja Sama
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="progress-group">
                                    <span class="progress-text">Sangat Kooperatif (90)</span>
                                    <span class="float-right"><b>2</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 28.6%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Kooperatif (80)</span>
                                    <span class="float-right"><b>2</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 28.6%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Cukup Kooperatif (75)</span>
                                    <span class="float-right"><b>2</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: 28.6%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Tidak Kooperatif (60)</span>
                                    <span class="float-right"><b>1</b>/7 dosen</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" style="width: 14.2%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Stats -->
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Ringkasan
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Rata-rata Nilai</span>
                                        <span class="info-box-number text-center text-muted mb-0">78.6</span>
                                    </div>
                                </div>
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Nilai Tertinggi</span>
                                        <span class="info-box-number text-center text-muted mb-0">90</span>
                                    </div>
                                </div>
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Nilai Terendah</span>
                                        <span class="info-box-number text-center text-muted mb-0">60</span>
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

<!-- Cooperation Level Change Confirmation Modal -->
<div class="modal fade" id="cooperationModal" tabindex="-1" role="dialog" aria-labelledby="cooperationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cooperationModalLabel">Konfirmasi Perubahan Tingkat Kerja Sama</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('cooperation/update-level') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="cooperationLecturerId">
                    <input type="hidden" name="level" id="cooperationLevel">
                    <p id="cooperationConfirmText">Apakah Anda yakin ingin mengubah tingkat kerja sama dosen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetCooperationRadio()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    // Cooperation level change confirmation
    let currentLecturerCooperation = {};

    function confirmCooperationChange(lecturerId, level) {
        // Store current level in case user cancels
        if (!currentLecturerCooperation[lecturerId]) {
            // This is the first time we're changing this lecturer's cooperation level
            // Get the current value from the checked radio button
            const checkedButton = document.querySelector(`input[name="cooperation_${lecturerId}"]:checked`);
            if (checkedButton) {
                currentLecturerCooperation[lecturerId] = parseInt(checkedButton.value);
            }
        }

        // Set modal values
        document.getElementById('cooperationLecturerId').value = lecturerId;
        document.getElementById('cooperationLevel').value = level;

        // Set confirmation message with appropriate level text
        let levelText = '';
        switch (level) {
            case 90:
                levelText = 'Sangat Kooperatif';
                break;
            case 80:
                levelText = 'Kooperatif';
                break;
            case 75:
                levelText = 'Cukup Kooperatif';
                break;
            case 60:
                levelText = 'Tidak Kooperatif';
                break;
        }

        document.getElementById('cooperationConfirmText').textContent =
            `Apakah Anda yakin ingin mengubah tingkat kerja sama dosen ini menjadi "${levelText}" (${level})?`;

        // Show modal
        $('#cooperationModal').modal('show');
    }

    // Reset radio button if user cancels
    function resetCooperationRadio() {
        const lecturerId = document.getElementById('cooperationLecturerId').value;
        const level = currentLecturerCooperation[lecturerId];

        if (level !== undefined) {
            // Select the appropriate radio button based on the original value
            document.querySelector(`input[name="cooperation_${lecturerId}"][value="${level}"]`).checked = true;

            // Update the label active state
            document.querySelectorAll(`label.btn input[name="cooperation_${lecturerId}"]`).forEach(input => {
                if (parseInt(input.value) === level) {
                    input.parentElement.classList.add('active');
                } else {
                    input.parentElement.classList.remove('active');
                }
            });
        }
    }
</script>

<!-- Custom CSS for radio button groups -->
<style>
    .btn-group-toggle .btn {
        padding: 0.15rem 0.5rem;
        font-size: 0.75rem;
    }

    .btn-outline-success.active,
    .btn-outline-success:active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }

    .btn-outline-primary.active,
    .btn-outline-primary:active {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .btn-outline-info.active,
    .btn-outline-info:active {
        background-color: #17a2b8 !important;
        border-color: #17a2b8 !important;
        color: white !important;
    }

    .btn-outline-danger.active,
    .btn-outline-danger:active {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }
</style>

<?= $this->endSection() ?>