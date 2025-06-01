<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Orientasi Pelayanan Dosen Fakultas</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="<?= base_url('orientation/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="<?= base_url('orientation/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <button type="button" class="btn btn-sm btn-info" onclick="refreshOrientationData()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                </button>
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

    <!-- Auto-calculation notice -->
    <div class="card-body p-2" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Sistem Auto-Kalkulasi:</strong> Skor dihitung ulang otomatis setiap kali halaman dimuat.
            Data orientasi pelayanan bersifat readonly dan tidak dapat dimanipulasi melalui interface ini.
            Total dosen: <strong><?= count($orientationData ?? []) ?></strong>
            <?php if (isset($calculationResult)): ?>
                <?php if ($calculationResult['added'] > 0): ?>
                    | <span class="text-success">Baru ditambahkan: <strong><?= $calculationResult['added'] ?></strong>
                        record</span>
                <?php endif; ?>
                <?php if ($calculationResult['updated'] > 0): ?>
                    | <span class="text-primary">Diperbarui: <strong><?= $calculationResult['updated'] ?></strong> skor</span>
                <?php endif; ?>
            <?php endif; ?>
            | Semester:
            <strong><?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?></strong>
        </small>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Dosen</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center">Program Studi</th>
                    <th class="text-center">Nilai Angket</th>
                    <th class="text-center">Nilai Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orientationData)): ?>
                    <?php foreach ($orientationData as $i => $lecturer): ?>
                        <?php
                        $score = (int)$lecturer['score'];

                        // Get score classification
                        if ($score >= 88) {
                            $scoreClass = 'text-success';
                            $badgeClass = 'badge-success';
                            $statusLabel = 'Sangat Baik';
                        } elseif ($score >= 80) {
                            $scoreClass = 'text-primary';
                            $badgeClass = 'badge-primary';
                            $statusLabel = 'Baik';
                        } elseif ($score >= 70) {
                            $scoreClass = 'text-warning';
                            $badgeClass = 'badge-warning';
                            $statusLabel = 'Cukup';
                        } else {
                            $scoreClass = 'text-danger';
                            $badgeClass = 'badge-danger';
                            $statusLabel = 'Kurang';
                        }

                        // Format program study for display
                        $programStudyLabel = match ($lecturer['study_program']) {
                            'bisnis_digital' => 'Bisnis Digital',
                            'informatika' => 'Informatika',
                            'sistem_informasi' => 'Sistem Informasi',
                            'sains_data' => 'Sains Data',
                            'magister_teknologi_informasi' => 'Magister TI',
                            default => $lecturer['study_program'] ?? '-'
                        };
                        ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <div>
                                    <strong><?= esc($lecturer['lecturer_name']) ?></strong>
                                    <?php if (!empty($lecturer['nip'])): ?>
                                        <br><small class="text-muted">NIP: <?= esc($lecturer['nip']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info"><?= esc($lecturer['position'] ?? '-') ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary"><?= esc($programStudyLabel) ?></span>
                            </td>
                            <td class="text-center">
                                <!-- Readonly questionnaire score display -->
                                <div class="readonly-score-display">
                                    <span class="badge badge-light border" style="font-size: 0.9rem; padding: 0.4rem 0.6rem;">
                                        <?= number_format((float)$lecturer['questionnaire_score'], 2) ?>/4.0
                                    </span>
                                </div>
                            </td>
                            <td class="text-center font-weight-bold <?= $scoreClass ?>"
                                id="score_<?= $lecturer['lecturer_id'] ?>"><?= $score ?></td>
                            <td class="text-center" id="status_<?= $lecturer['lecturer_id'] ?>">
                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data dosen untuk semester ini</p>
                                <button type="button" class="btn btn-primary" onclick="refreshOrientationData()">
                                    <i class="fas fa-sync-alt mr-1"></i> Muat Data Dosen
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Information footer -->
    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
                <small class="text-muted">
                    <i class="fas fa-lock mr-1"></i>
                    Data ini bersifat readonly. Nilai angket dikelola melalui sistem terpisah.
                </small>
            </div>
            <div class="col-sm-6 text-right">
                <small class="text-muted">
                    Terakhir diperbarui: <?= date('d/m/Y H:i:s') ?>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    function refreshOrientationData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data orientasi pelayanan dosen dan menghitung ulang skor',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Auto-refresh page after 1 second
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
</script>

<style>
    .readonly-score-display {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .readonly-score-display .badge {
        min-width: 60px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        background-color: #f8f9fa !important;
        color: #495057 !important;
        border: 1px solid #dee2e6 !important;
    }

    /* Disabled/readonly styling */
    .readonly-indicator {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Remove any hover effects for readonly elements */
    .readonly-score-display:hover {
        cursor: default;
    }
</style>