<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Kerja Sama Dosen Fakultas</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="<?= base_url('cooperation/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="<?= base_url('cooperation/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <button type="button" class="btn btn-sm btn-info" onclick="refreshCooperationData()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                </button>
                <a href="<?= base_url('cooperation/recalculate-scores') ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-calculator mr-1"></i> Hitung Ulang Semua
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

    <!-- Auto-population notice -->
    <div class="card-body p-2" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Data kerja sama dosen diperbarui secara otomatis setiap kali halaman dimuat.
            <strong>Skor akan dihitung ulang secara otomatis</strong> setiap kali ada perubahan pada tingkat kerja sama.
            Total dosen: <strong><?= count($lecturersData) ?></strong>
            <?php if (isset($autoPopulationResult) && $autoPopulationResult['added'] > 0): ?>
                | <span class="text-success">Baru ditambahkan: <strong><?= $autoPopulationResult['added'] ?></strong>
                    record</span>
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
                    <th class="text-center">Level Kerjasama</th>
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lecturersData)): ?>
                    <?php foreach ($lecturersData as $i => $lecturer): ?>
                        <?php
                        // Calculate score with improved logic
                        $score = $lecturer['score'] ?: 0;
                        if ($score == 0) {
                            // Use the same calculation logic as the model
                            $levelValues = [
                                'very_cooperative' == 88,
                                'cooperative' => 80,
                                'fair' => 75,
                                'not_cooperative' => 60
                            ];
                            $score = $levelValues[$lecturer['level']] ?? 60;
                        }

                        // Get score classification
                        if ($score >= 88) {
                            $scoreClass = 'text-success';
                            $badgeClass = 'badge-success';
                            $statusLabel = 'Sangat Baik';
                        } elseif ($score >= 76) {
                            $scoreClass = 'text-primary';
                            $badgeClass = 'badge-primary';
                            $statusLabel = 'Baik';
                        } elseif ($score >= 61) {
                            $scoreClass = 'text-info';
                            $badgeClass = 'badge-info';
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
                                <select class="form-control form-control-sm cooperation-level-dropdown"
                                    data-lecturer-id="<?= $lecturer['lecturer_id'] ?>"
                                    onchange="confirmCooperationChange(<?= $lecturer['lecturer_id'] ?>, this.value)"
                                    style="width: 180px; margin: 0 auto; cursor: pointer;">
                                    <option value="not_cooperative"
                                        <?= $lecturer['level'] === 'not_cooperative' ? 'selected' : '' ?>>
                                        Tidak Kooperatif
                                    </option>
                                    <option value="fair" <?= $lecturer['level'] === 'fair' ? 'selected' : '' ?>>
                                        Cukup Kooperatif
                                    </option>
                                    <option value="cooperative" <?= $lecturer['level'] === 'cooperative' ? 'selected' : '' ?>>
                                        Kooperatif
                                    </option>
                                    <option value="very_cooperative"
                                        <?= $lecturer['level'] === 'very_cooperative' ? 'selected' : '' ?>>
                                        Sangat Kooperatif
                                    </option>
                                </select>
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
                                <button type="button" class="btn btn-primary" onclick="refreshCooperationData()">
                                    <i class="fas fa-sync-alt mr-1"></i> Muat Data Dosen
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function refreshCooperationData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data kerja sama dosen dan menghitung ulang skor',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    // Search functionality
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

<style>
    .cooperation-level-dropdown {
        border-width: 2px !important;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .cooperation-level-dropdown:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    .cooperation-level-dropdown option {
        color: #495057 !important;
        background-color: white !important;
    }

    /* Color styling for different levels */
    .border-danger {
        border-color: #dc3545 !important;
    }

    .border-warning {
        border-color: #ffc107 !important;
    }

    .border-info {
        border-color: #17a2b8 !important;
    }

    .border-success {
        border-color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-warning {
        color: #856404 !important;
    }

    .text-info {
        color: #17a2b8 !important;
    }

    .text-success {
        color: #28a745 !important;
    }
</style>