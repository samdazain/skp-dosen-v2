<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Komitmen Dosen Fakultas</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="<?= base_url('commitment/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="<?= base_url('commitment/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <button type="button" class="btn btn-sm btn-info" onclick="refreshCommitmentData()">
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

    <!-- Auto-population notice -->
    <div class="card-body p-2" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Data komitmen dosen diperbarui secara otomatis setiap kali halaman dimuat.
            <strong>Skor akan dihitung ulang secara otomatis</strong> setiap kali ada perubahan pada kompetensi atau Tri
            Dharma.
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
                    <th class="text-center">Kompetensi (Aktif)</th>
                    <th class="text-center">Tri Dharma (BKD)</th>
                    <th class="text-center">Nilai Rata-rata</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lecturersData)): ?>
                    <?php foreach ($lecturersData as $i => $lecturer): ?>
                        <?php
                        // Calculate average score with improved logic
                        $averageScore = $lecturer['score'] ?: 0;
                        if ($averageScore == 0) {
                            // Use the same calculation logic as the model
                            $competenceValue = ($lecturer['competence'] === 'active') ? 88 : 70;
                            $triDharmaValue = ($lecturer['tridharma_pass'] == 1) ? 88 : 70;
                            $averageScore = round(($competenceValue + $triDharmaValue) / 2);
                        }

                        // Ensure we always have a valid score
                        if ($averageScore == 0) {
                            // Fallback calculation
                            $competenceValue = ($lecturer['competence'] === 'active') ? 88 : 70;
                            $triDharmaValue = ($lecturer['tridharma_pass'] == 1) ? 88 : 70;
                            $averageScore = round(($competenceValue + $triDharmaValue) / 2);
                        }

                        // Get score classification
                        if ($averageScore >= 90) {
                            $scoreClass = 'text-success';
                            $badgeClass = 'badge-success';
                            $statusLabel = 'Sangat Baik';
                        } elseif ($averageScore >= 76) {
                            $scoreClass = 'text-primary';
                            $badgeClass = 'badge-primary';
                            $statusLabel = 'Baik';
                        } elseif ($averageScore >= 61) {
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
                        <tr data-position="<?= esc($lecturer['position']) ?>"
                            data-program="<?= esc($lecturer['study_program']) ?>">
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
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label
                                        class="btn btn-xs btn-outline-success <?= $lecturer['competence'] === 'active' ? 'active' : '' ?>">
                                        <input type="radio" name="competency_<?= $lecturer['lecturer_id'] ?>" value="yes"
                                            <?= $lecturer['competence'] === 'active' ? 'checked' : '' ?>
                                            onclick="confirmCompetencyChange(<?= $lecturer['lecturer_id'] ?>, true)"> Ya
                                    </label>
                                    <label
                                        class="btn btn-xs btn-outline-danger <?= $lecturer['competence'] !== 'active' ? 'active' : '' ?>">
                                        <input type="radio" name="competency_<?= $lecturer['lecturer_id'] ?>" value="no"
                                            <?= $lecturer['competence'] !== 'active' ? 'checked' : '' ?>
                                            onclick="confirmCompetencyChange(<?= $lecturer['lecturer_id'] ?>, false)">
                                        Tidak
                                    </label>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label
                                        class="btn btn-xs btn-outline-success <?= $lecturer['tridharma_pass'] == 1 ? 'active' : '' ?>">
                                        <input type="radio" name="tri_dharma_<?= $lecturer['lecturer_id'] ?>" value="pass"
                                            <?= $lecturer['tridharma_pass'] == 1 ? 'checked' : '' ?>
                                            onclick="confirmTriDharmaChange(<?= $lecturer['lecturer_id'] ?>, true)">
                                        Lulus
                                    </label>
                                    <label
                                        class="btn btn-xs btn-outline-danger <?= $lecturer['tridharma_pass'] != 1 ? 'active' : '' ?>">
                                        <input type="radio" name="tri_dharma_<?= $lecturer['lecturer_id'] ?>" value="fail"
                                            <?= $lecturer['tridharma_pass'] != 1 ? 'checked' : '' ?>
                                            onclick="confirmTriDharmaChange(<?= $lecturer['lecturer_id'] ?>, false)">
                                        Tidak
                                    </label>
                                </div>
                            </td>
                            <td class="text-center font-weight-bold <?= $scoreClass ?>"><?= $averageScore ?></td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data dosen untuk semester ini</p>
                                <button type="button" class="btn btn-primary" onclick="refreshCommitmentData()">
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
    function refreshCommitmentData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Sedang menyinkronkan data komitmen dosen dan menghitung ulang skor',
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

    // Ensure jQuery and other dependencies are loaded for AJAX functionality
    $(document).ready(function() {
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                }
            }
        });
    });
</script>

<!-- Include the enhanced scripts for real-time updates -->
<?= view('commitment/partials/scripts') ?>