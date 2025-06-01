<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Master SKP Dosen Fakultas</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="<?= base_url('skp/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="<?= base_url('skp/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <button type="button" class="btn btn-sm btn-info" onclick="refreshSKPData()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                </button>
                <a href="<?= base_url('skp/recalculate-scores') ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-calculator mr-1"></i> Hitung Ulang
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

    <!-- Auto-calculation notice -->
    <div class="card-body p-2" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Sistem Auto-Kalkulasi:</strong> Skor SKP dihitung otomatis dari rata-rata 5 komponen penilaian.
            Data diperbarui secara real-time setiap kali halaman dimuat.
            Total dosen: <strong><?= count($skpData ?? []) ?></strong>
            <?php if (isset($calculationResult)): ?>
                <?php if ($calculationResult['added'] > 0): ?>
                    | <span class="text-success">Baru ditambahkan: <strong><?= $calculationResult['added'] ?></strong> record</span>
                <?php endif; ?>
                <?php if ($calculationResult['updated'] > 0): ?>
                    | <span class="text-primary">Diperbarui: <strong><?= $calculationResult['updated'] ?></strong> skor</span>
                <?php endif; ?>
            <?php endif; ?>
            | Semester: <strong><?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?></strong>
        </small>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Nama Dosen</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center">Program Studi</th>
                    <th class="text-center">Integritas</th>
                    <th class="text-center">Disiplin</th>
                    <th class="text-center">Komitmen</th>
                    <th class="text-center">Kerjasama</th>
                    <th class="text-center">Orientasi Pelayanan</th>
                    <th class="text-center">Nilai SKP</th>
                    <th class="text-center">Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($skpData)): ?>
                    <?php foreach ($skpData as $i => $lecturer): ?>
                        <?php
                        $skpScore = (float)$lecturer['skp_score'];
                        $category = $lecturer['skp_category'];

                        // Get category badge class
                        $badgeClass = match ($category) {
                            'Sangat Baik' => 'badge-sangat-baik',
                            'Baik' => 'badge-baik',
                            'Cukup' => 'badge-cukup',
                            'Kurang' => 'badge-kurang',
                            default => 'badge-belum-dinilai'
                        };

                        // Get SKP score color class
                        $scoreClass = match ($category) {
                            'Sangat Baik' => 'text-success',
                            'Baik' => 'text-primary',
                            'Cukup' => 'text-warning',
                            'Kurang' => 'text-danger',
                            default => 'text-muted'
                        };

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
                                <span class="component-score"><?= (int)$lecturer['integrity_score'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="component-score"><?= (int)$lecturer['discipline_score'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="component-score"><?= (int)$lecturer['commitment_score'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="component-score"><?= (int)$lecturer['cooperation_score'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="component-score"><?= (int)$lecturer['orientation_score'] ?></span>
                            </td>
                            <td class="text-center">
                                <div class="skp-score-display">
                                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.9rem; padding: 0.4rem 0.6rem;">
                                        <?= number_format($skpScore, 1) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $category ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data SKP untuk semester ini</p>
                                <button type="button" class="btn btn-primary" onclick="refreshSKPData()">
                                    <i class="fas fa-sync-alt mr-1"></i> Muat Data SKP
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
                    <i class="fas fa-info-circle mr-1"></i>
                    Data SKP dihitung otomatis dari nilai komponen: Integritas, Disiplin, Komitmen, Kerjasama, dan Orientasi Pelayanan.
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
    function refreshSKPData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data SKP dan menghitung ulang skor dari semua komponen',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Auto-refresh page after 1.5 seconds
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
</script>