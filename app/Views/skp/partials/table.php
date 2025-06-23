<div class="card shadow-lg border-0">
    <div class="card-header bg-gradient-success text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-chart-line mr-2"></i>
            Data Master SKP Dosen Fakultas
        </h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="<?= base_url('skp/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-light btn-sm hover-lift">
                    <i class="fas fa-file-excel mr-1 text-success"></i> Export Excel
                </a>
                <a href="<?= base_url('skp/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                    class="btn btn-light btn-sm hover-lift">
                    <i class="fas fa-file-pdf mr-1 text-danger"></i> Export PDF
                </a>
                <button type="button" class="btn btn-light btn-sm hover-lift" onclick="refreshSKPData()">
                    <i class="fas fa-sync-alt mr-1 text-info"></i> Refresh Data
                </button>
            </div>
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="table_search" class="form-control" id="searchInput"
                    placeholder="Cari berdasarkan nama...">
                <div class="input-group-append">
                    <button type="button" class="btn btn-light">
                        <i class="fas fa-search text-success"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-calculation notice -->
    <div class="card-body p-3 bg-light border-bottom">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-gradient-info text-white mr-3">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="flex-grow-1">
                <small class="text-muted d-block">
                    <strong class="text-success">Sistem Auto-Kalkulasi:</strong>
                    Nilai SKP dihitung otomatis dari rata-rata 5 komponen penilaian.
                </small>
                <small class="text-muted">
                    Total dosen: <strong class="text-primary"><?= count($skpData) ?></strong>
                    <?php if (isset($calculationResult)): ?>
                        <?php if ($calculationResult['updated'] > 0): ?>
                            | <span class="text-success">
                                <i class="fas fa-check-circle"></i>
                                Diperbarui: <strong><?= $calculationResult['updated'] ?></strong> nilai
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                    | Semester: <strong
                        class="text-info"><?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?></strong>
                </small>
            </div>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped modern-table">
            <thead class="bg-gradient-success text-white">
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
                    <!-- <th class="text-center">Kategori</th> -->
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($skpData)): ?>
                    <?php foreach ($skpData as $i => $lecturer): ?>
                        <?php
                        $skpScore = (float)$lecturer['skp_score'];
                        $category = $lecturer['skp_category'];

                        // Get score and category styling
                        // if ($skpScore >= 88) {
                        //     $scoreClass = 'text-success font-weight-bold';
                        //     $badgeClass = 'badge-success';
                        //     $progressClass = 'bg-success';
                        //     $progressWidth = min(100, ($skpScore / 100) * 100);
                        // } elseif ($skpScore >= 76) {
                        //     $scoreClass = 'text-primary font-weight-bold';
                        //     $badgeClass = 'badge-primary';
                        //     $progressClass = 'bg-primary';
                        //     $progressWidth = min(100, ($skpScore / 100) * 100);
                        // } elseif ($skpScore >= 61) {
                        //     $scoreClass = 'text-warning font-weight-bold';
                        //     $badgeClass = 'badge-warning';
                        //     $progressClass = 'bg-warning';
                        //     $progressWidth = min(100, ($skpScore / 100) * 100);
                        // } else {
                        //     $scoreClass = 'text-danger font-weight-bold';
                        //     $badgeClass = 'badge-danger';
                        //     $progressClass = 'bg-danger';
                        //     $progressWidth = min(100, max(10, ($skpScore / 100) * 100));
                        // }

                        // Format program study
                        $programStudyLabel = match ($lecturer['study_program']) {
                            'bisnis_digital' => 'Bisnis Digital',
                            'informatika' => 'Informatika',
                            'sistem_informasi' => 'Sistem Informasi',
                            'sains_data' => 'Sains Data',
                            'magister_teknologi_informasi' => 'Magister TI',
                            default => $lecturer['study_program'] ?? '-'
                        };
                        ?>
                        <tr class="table-row-hover" data-position="<?= esc($lecturer['position']) ?>"
                            data-program="<?= esc($lecturer['study_program']) ?>">
                            <td class="text-center align-middle">
                                <div class="row-number">
                                    <?= $i + 1 ?>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="lecturer-info">
                                    <div class="lecturer-name"><?= esc($lecturer['lecturer_name']) ?></div>
                                    <?php if (!empty($lecturer['nip'])): ?>
                                        <small class="text-muted">NIP: <?= esc($lecturer['nip']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light position-badge"><?= esc($lecturer['position'] ?? '-') ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-info program-badge"><?= esc($programStudyLabel) ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="score-cell">
                                    <span class="component-score" data-toggle="tooltip" title="Integritas">
                                        <?= (int)$lecturer['integrity_score'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="score-cell">
                                    <span class="component-score" data-toggle="tooltip" title="Disiplin">
                                        <?= (int)$lecturer['discipline_score'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="score-cell">
                                    <span class="component-score" data-toggle="tooltip" title="Komitmen">
                                        <?= (int)$lecturer['commitment_score'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="score-cell">
                                    <span class="component-score" data-toggle="tooltip" title="Kerjasama">
                                        <?= (int)$lecturer['cooperation_score'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="score-cell">
                                    <span class="component-score" data-toggle="tooltip" title="Orientasi Pelayanan">
                                        <?= (int)$lecturer['orientation_score'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="skp-score-container">
                                    <div data-toggle="tooltip" title="Nilai SKP: <?= number_format($skpScore, 1) ?>">
                                        <?= number_format($skpScore, 1) ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                </div>
                                <h5 class="text-muted">Belum ada data SKP</h5>
                                <p class="text-muted mb-3">Data SKP akan muncul secara otomatis ketika nilai komponen sudah
                                    diinput
                                </p>
                                <button type="button" class="btn btn-success hover-lift" onclick="refreshSKPData()">
                                    <i class="fas fa-sync-alt mr-1"></i> Muat Data SKP
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Modern table styling with green gradients */
    .modern-table {
        border: none;
    }

    .modern-table thead th {
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }

    .table-row-hover {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(40, 167, 69, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
        border-left: 3px solid #28a745;
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
    }

    .lecturer-info {
        transition: all 0.3s ease;
    }

    .lecturer-name {
        font-weight: 600;
        color: #2c3e50;
        transition: color 0.3s ease;
    }

    .table-row-hover:hover .lecturer-name {
        color: #28a745;
    }

    .row-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 auto;
        transition: all 0.3s ease;
    }

    .table-row-hover:hover .row-number {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .score-cell {
        position: relative;
    }

    .component-score {
        display: inline-block;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border: 2px solid #28a745;
        color: #28a745;
        font-weight: 600;
        line-height: 31px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: help;
    }

    .component-score:hover {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .skp-score-container {
        padding: 0.5rem;
    }

    .skp-score {
        font-size: 1.2rem;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .position-badge,
    .program-badge,
    .category-badge {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .position-badge:hover,
    .program-badge:hover,
    .category-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .empty-state {
        padding: 3rem 1rem;
    }

    .empty-icon {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* Enhanced card styling */
    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        border-radius: 15px 15px 0 0 !important;
        border: none;
    }

    /* Progress bar animation */
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }

    /* Tooltip styling */
    .tooltip-inner {
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 6px;
    }

    .tooltip.bs-tooltip-top .arrow::before {
        border-top-color: #28a745;
    }

    /* Search input styling */
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    /* Button enhancements */
    .btn-light {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-color: #28a745;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
    }
</style>

<script>
    function refreshSKPData() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat Data SKP...',
                text: 'Sedang menyinkronkan dan menghitung ulang nilai SKP',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }

    function viewLecturerDetails(lecturerId) {
        // Implementation for viewing lecturer details
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Detail Komponen SKP',
                text: 'Fitur detail komponen sedang dalam pengembangan',
                icon: 'info',
                confirmButtonColor: '#28a745'
            });
        }
    }

    // Initialize tooltips
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        // Search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('.modern-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>