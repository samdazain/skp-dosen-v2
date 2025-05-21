<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Orientasi Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Orientasi</li>
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
                        <h3 class="card-title">Data Orientasi Dosen Fakultas</h3>
                        <div class="card-tools">
                            <div class="btn-group mr-2">
                                <a href="<?= base_url('orientation/export-excel') ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                                </a>
                                <a href="<?= base_url('orientation/export-pdf') ?>" class="btn btn-sm btn-danger">
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
                                    <th class="text-center">Nilai Angket Pengajaran</th>
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
                                        'questionnaire_score' => 3.8
                                    ],
                                    [
                                        'id' => 2,
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'questionnaire_score' => 3.2
                                    ],
                                    [
                                        'id' => 3,
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'questionnaire_score' => 3.7
                                    ],
                                    [
                                        'id' => 4,
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'questionnaire_score' => 2.75
                                    ],
                                    [
                                        'id' => 5,
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'questionnaire_score' => 2.6
                                    ],
                                    [
                                        'id' => 6,
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'questionnaire_score' => 3.5
                                    ],
                                    [
                                        'id' => 7,
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'questionnaire_score' => 2.3
                                    ]
                                ];

                                // Calculate total score based on questionnaire score
                                function calculateTotalScore($questionnaireScore)
                                {
                                    if ($questionnaireScore >= 3.5) {
                                        return 88;
                                    } elseif ($questionnaireScore >= 3.0) {
                                        return 85;
                                    } elseif ($questionnaireScore >= 2.75) {
                                        return 80;
                                    } elseif ($questionnaireScore >= 2.5) {
                                        return 70;
                                    } else {
                                        return 60;
                                    }
                                }

                                // Get status based on total score
                                function getStatus($totalScore)
                                {
                                    if ($totalScore >= 85) return ['text-success', 'badge-success', 'Sangat Baik'];
                                    if ($totalScore >= 80) return ['text-primary', 'badge-primary', 'Baik'];
                                    if ($totalScore >= 70) return ['text-warning', 'badge-warning', 'Cukup'];
                                    return ['text-danger', 'badge-danger', 'Kurang'];
                                }

                                foreach ($dummyData as $i => $lecturer):
                                    $totalScore = calculateTotalScore($lecturer['questionnaire_score']);
                                    list($textClass, $badgeClass, $statusLabel) = getStatus($totalScore);
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm" style="width: 150px; margin: 0 auto;">
                                                <input type="number" class="form-control text-center"
                                                    value="<?= $lecturer['questionnaire_score'] ?>" min="0" max="4"
                                                    step="0.1" data-lecturer-id="<?= $lecturer['id'] ?>"
                                                    onchange="confirmScoreChange(this, <?= $lecturer['id'] ?>)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">/4.0</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-bold <?= $textClass ?>"><?= $totalScore ?></td>
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

                <!-- Info Cards -->
                <div class="row mt-4">
                    <!-- Questionnaire Score Range Info -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Panduan Penilaian
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nilai Angket</th>
                                            <th class="text-center">Nilai Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>≥ 3.5</td>
                                            <td class="text-center text-success font-weight-bold">88</td>
                                        </tr>
                                        <tr>
                                            <td>3.0 - 3.49</td>
                                            <td class="text-center text-primary font-weight-bold">85</td>
                                        </tr>
                                        <tr>
                                            <td>= 2.75</td>
                                            <td class="text-center text-info font-weight-bold">80</td>
                                        </tr>
                                        <tr>
                                            <td>2.5 - 2.74</td>
                                            <td class="text-center text-warning font-weight-bold">70</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                < 2.5</td>
                                            <td class="text-center text-danger font-weight-bold">60</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Statistik
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Average Questionnaire Score -->
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Rata-rata Nilai Angket</span>
                                        <span class="info-box-number text-center text-muted mb-0">3.12</span>
                                    </div>
                                </div>

                                <!-- Status Distribution -->
                                <div class="mt-4">
                                    <h6 class="text-center">Distribusi Status</h6>
                                    <div class="progress-group">
                                        <span class="progress-text">Sangat Baik</span>
                                        <span class="float-right"><b>3</b>/7 dosen</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 42.9%"></div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <span class="progress-text">Baik</span>
                                        <span class="float-right"><b>1</b>/7 dosen</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" style="width: 14.3%"></div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <span class="progress-text">Cukup</span>
                                        <span class="float-right"><b>2</b>/7 dosen</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: 28.6%"></div>
                                        </div>
                                    </div>
                                    <div class="progress-group">
                                        <span class="progress-text">Kurang</span>
                                        <span class="float-right"><b>1</b>/7 dosen</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: 14.3%"></div>
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

<!-- Score Change Confirmation Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1" role="dialog" aria-labelledby="scoreModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scoreModalLabel">Konfirmasi Perubahan Nilai Angket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('orientation/update-score') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="scoreLecturerId">
                    <input type="hidden" name="score" id="scoreValue">

                    <p>Apakah Anda yakin ingin mengubah nilai angket pengajaran menjadi <strong
                            id="scoreDisplayValue"></strong>?</p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> Perubahan nilai angket akan mempengaruhi nilai total dan
                        status dosen.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetScoreInput()">Batal</button>
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

    // Store original values
    let originalScores = {};
    let inputElement = null;

    // Score change confirmation
    function confirmScoreChange(element, lecturerId) {
        // Store the input element and original value
        inputElement = element;
        if (!originalScores[lecturerId]) {
            originalScores[lecturerId] = parseFloat(element.defaultValue);
        }

        // Validate input
        let score = parseFloat(element.value);
        if (isNaN(score) || score < 0 || score > 4) {
            alert('Nilai angket harus dalam rentang 0-4');
            element.value = originalScores[lecturerId];
            return;
        }

        // Set modal values
        document.getElementById('scoreLecturerId').value = lecturerId;
        document.getElementById('scoreValue').value = score;
        document.getElementById('scoreDisplayValue').textContent = score;

        // Show modal
        $('#scoreModal').modal('show');
    }

    // Reset score input if user cancels
    function resetScoreInput() {
        if (inputElement) {
            const lecturerId = inputElement.getAttribute('data-lecturer-id');
            if (originalScores[lecturerId] !== undefined) {
                inputElement.value = originalScores[lecturerId];
            }
        }
    }
</script>

<?= $this->endSection() ?>