<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Komitmen Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Komitmen</li>
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
                        <h3 class="card-title">Data Komitmen Dosen Fakultas</h3>
                        <div class="card-tools">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('commitment/export-excel') ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('commitment/export-pdf') ?>" class="btn btn-sm btn-danger">
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
                                    <th class="text-center">Kompetensi (Aktif)</th>
                                    <th class="text-center">Tri Dharma (BKD)</th>
                                    <th class="text-center">Nilai Rata-rata</th>
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
                                        'competency' => true,
                                        'tri_dharma' => true
                                    ],
                                    [
                                        'id' => 2,
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'competency' => true,
                                        'tri_dharma' => true
                                    ],
                                    [
                                        'id' => 3,
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'competency' => true,
                                        'tri_dharma' => true
                                    ],
                                    [
                                        'id' => 4,
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'competency' => false,
                                        'tri_dharma' => true
                                    ],
                                    [
                                        'id' => 5,
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'competency' => true,
                                        'tri_dharma' => false
                                    ],
                                    [
                                        'id' => 6,
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'competency' => true,
                                        'tri_dharma' => true
                                    ],
                                    [
                                        'id' => 7,
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'competency' => false,
                                        'tri_dharma' => false
                                    ]
                                ];

                                // Calculate average score (now just based on competency and tri_dharma)
                                function calculateAverageScore($competency, $triDharma)
                                {
                                    $competencyValue = $competency ? 100 : 0;
                                    $triDharmaValue = $triDharma ? 100 : 0;

                                    // Equal weighting (50% each) now that we don't have final_score
                                    return round(($competencyValue * 0.5) + ($triDharmaValue * 0.5), 1);
                                }

                                // Get score class and status
                                function getScoreInfo($score)
                                {
                                    if ($score >= 90) return ['text-success', 'badge-success', 'Sangat Baik'];
                                    if ($score >= 76) return ['text-primary', 'badge-primary', 'Baik'];
                                    if ($score >= 61) return ['text-warning', 'badge-warning', 'Cukup'];
                                    return ['text-danger', 'badge-danger', 'Kurang'];
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    $averageScore = calculateAverageScore(
                                        $lecturer['competency'],
                                        $lecturer['tri_dharma']
                                    );
                                    list($scoreClass, $badgeClass, $statusLabel) = getScoreInfo($averageScore);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label
                                                    class="btn btn-xs btn-outline-success <?= $lecturer['competency'] ? 'active' : '' ?>">
                                                    <input type="radio" name="competency_<?= $lecturer['id'] ?>" value="yes"
                                                        <?= $lecturer['competency'] ? 'checked' : '' ?>
                                                        onclick="confirmCompetencyChange(<?= $lecturer['id'] ?>, true)"> Ya
                                                </label>
                                                <label
                                                    class="btn btn-xs btn-outline-danger <?= !$lecturer['competency'] ? 'active' : '' ?>">
                                                    <input type="radio" name="competency_<?= $lecturer['id'] ?>" value="no"
                                                        <?= !$lecturer['competency'] ? 'checked' : '' ?>
                                                        onclick="confirmCompetencyChange(<?= $lecturer['id'] ?>, false)">
                                                    Tidak
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label
                                                    class="btn btn-xs btn-outline-success <?= $lecturer['tri_dharma'] ? 'active' : '' ?>">
                                                    <input type="radio" name="tri_dharma_<?= $lecturer['id'] ?>"
                                                        value="pass" <?= $lecturer['tri_dharma'] ? 'checked' : '' ?>
                                                        onclick="confirmTriDharmaChange(<?= $lecturer['id'] ?>, true)">
                                                    Lulus
                                                </label>
                                                <label
                                                    class="btn btn-xs btn-outline-danger <?= !$lecturer['tri_dharma'] ? 'active' : '' ?>">
                                                    <input type="radio" name="tri_dharma_<?= $lecturer['id'] ?>"
                                                        value="fail" <?= !$lecturer['tri_dharma'] ? 'checked' : '' ?>
                                                        onclick="confirmTriDharmaChange(<?= $lecturer['id'] ?>, false)">
                                                    Tidak
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold <?= $scoreClass ?>"><?= $averageScore ?>
                                        </td>
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
                    <!-- Competency Stats -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Status Kompetensi
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Aktif</span>
                                                <span class="info-box-number">5</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 71.4%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    71.4% dari total dosen
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tidak Aktif</span>
                                                <span class="info-box-number">2</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 28.6%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    28.6% dari total dosen
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tri Dharma Stats -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-university mr-1"></i>
                                    Status Tri Dharma
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Lulus</span>
                                                <span class="info-box-number">5</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 71.4%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    71.4% dari total dosen
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tidak Lulus</span>
                                                <span class="info-box-number">2</span>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 28.6%"></div>
                                                </div>
                                                <span class="progress-description">
                                                    28.6% dari total dosen
                                                </span>
                                            </div>
                                        </div>
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

<!-- Competency Confirmation Modal -->
<div class="modal fade" id="competencyModal" tabindex="-1" role="dialog" aria-labelledby="competencyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="competencyModalLabel">Konfirmasi Perubahan Status Kompetensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('commitment/update-competency') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="competencyLecturerId">
                    <input type="hidden" name="status" id="competencyStatus">
                    <p id="competencyConfirmText">Apakah Anda yakin ingin mengubah status kompetensi dosen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetCompetencyRadio()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tri Dharma Confirmation Modal -->
<div class="modal fade" id="triDharmaModal" tabindex="-1" role="dialog" aria-labelledby="triDharmaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="triDharmaModalLabel">Konfirmasi Perubahan Status Tri Dharma</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('commitment/update-tri-dharma') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="triDharmaLecturerId">
                    <input type="hidden" name="status" id="triDharmaStatus">
                    <p id="triDharmaConfirmText">Apakah Anda yakin ingin mengubah status Tri Dharma dosen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetTriDharmaRadio()">Batal</button>
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

    // Competency change confirmation
    let currentLecturerCompetency = {};

    function confirmCompetencyChange(lecturerId, status) {
        // Store current status in case user cancels
        if (!currentLecturerCompetency[lecturerId]) {
            // Get the opposite of what's being set now (since this is called before the value changes)
            currentLecturerCompetency[lecturerId] = !status;
        }

        // Set modal values
        document.getElementById('competencyLecturerId').value = lecturerId;
        document.getElementById('competencyStatus').value = status;

        // Set confirmation message
        const statusText = status ? 'aktif' : 'tidak aktif';
        document.getElementById('competencyConfirmText').textContent =
            `Apakah Anda yakin ingin mengubah status kompetensi dosen ini menjadi ${statusText}?`;

        // Show modal
        $('#competencyModal').modal('show');
    }

    // Reset radio button if user cancels
    function resetCompetencyRadio() {
        const lecturerId = document.getElementById('competencyLecturerId').value;
        const status = currentLecturerCompetency[lecturerId];

        if (status !== undefined) {
            // Select the appropriate radio button based on the original value
            const valueToSelect = status ? 'yes' : 'no';
            document.querySelector(`input[name="competency_${lecturerId}"][value="${valueToSelect}"]`).checked = true;

            // Update the label active state
            document.querySelectorAll(`label.btn input[name="competency_${lecturerId}"]`).forEach(input => {
                if (input.value === valueToSelect) {
                    input.parentElement.classList.add('active');
                } else {
                    input.parentElement.classList.remove('active');
                }
            });
        }
    }

    // Tri Dharma change confirmation
    let currentLecturerTriDharma = {};

    function confirmTriDharmaChange(lecturerId, status) {
        // Store current status in case user cancels
        if (!currentLecturerTriDharma[lecturerId]) {
            // Get the opposite of what's being set now
            currentLecturerTriDharma[lecturerId] = !status;
        }

        // Set modal values
        document.getElementById('triDharmaLecturerId').value = lecturerId;
        document.getElementById('triDharmaStatus').value = status;

        // Set confirmation message
        const statusText = status ? 'lulus' : 'tidak lulus';
        document.getElementById('triDharmaConfirmText').textContent =
            `Apakah Anda yakin ingin mengubah status Tri Dharma dosen ini menjadi ${statusText}?`;

        // Show modal
        $('#triDharmaModal').modal('show');
    }

    // Reset radio button if user cancels
    function resetTriDharmaRadio() {
        const lecturerId = document.getElementById('triDharmaLecturerId').value;
        const status = currentLecturerTriDharma[lecturerId];

        if (status !== undefined) {
            // Select the appropriate radio button based on the original value
            const valueToSelect = status ? 'pass' : 'fail';
            document.querySelector(`input[name="tri_dharma_${lecturerId}"][value="${valueToSelect}"]`).checked = true;

            // Update the label active state
            document.querySelectorAll(`label.btn input[name="tri_dharma_${lecturerId}"]`).forEach(input => {
                if (input.value === valueToSelect) {
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
        font-size: 0.85rem;
    }

    .btn-outline-success.active,
    .btn-outline-success:active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }

    .btn-outline-danger.active,
    .btn-outline-danger:active {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    .badge-pill {
        padding: 0.4rem 0.7rem;
        font-size: 0.9em;
    }
</style>

<?= $this->endSection() ?>