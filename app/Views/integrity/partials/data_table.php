<?php

/**
 * Integrity Data Table Partial
 * 
 * @var array $integrityData
 * @var array $currentSemester
 */
?>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0">
            <i class="fas fa-table mr-2"></i>
            Data Integritas Dosen
        </h3>
        <div class="card-tools d-flex align-items-center">
            <!-- Export Buttons -->
            <?= view('components/export_buttons', [
                'baseUrl' => 'integrity',
                'exportTypes' => ['excel', 'pdf']
            ]) ?>

            <!-- Search Bar -->
            <?= view('components/search_bar', [
                'placeholder' => 'Cari berdasarkan nama dosen...',
                'inputId' => 'integritySearch'
            ]) ?>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped" id="integrityTable">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th class="sortable" data-sort="name">
                        Nama Dosen
                        <i class="fas fa-sort sort-icon ml-1"></i>
                    </th>
                    <th class="text-center">NIP</th>
                    <th class="sortable text-center" data-sort="study_program">
                        Program Studi
                        <i class="fas fa-sort sort-icon ml-1"></i>
                    </th>
                    <th class="text-center">Kehadiran Mengajar</th>
                    <th class="text-center">Jumlah MK di Ampu</th>
                    <th class="text-center">Skor Integritas</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($integrityData)): ?>
                    <?php foreach ($integrityData as $index => $data): ?>
                        <tr data-position="<?= esc($data['position']) ?>"
                            data-study-program="<?= esc($data['study_program']) ?>">
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <div class="user-panel">
                                    <div class="info">
                                        <strong><?= esc($data['lecturer_name']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= esc($data['position']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="">
                                <?= esc($data['nip']) ?>
                            </td>
                            <td class="text-center">
                                <?= view('components/study_program_badge', [
                                    'program' => $data['study_program']
                                ]) ?>
                            </td>
                            <td class="text-center">
                                <?= (int)$data['teaching_attendance'] ?>
                            </td>
                            <td class="text-center">
                                <?= (int)$data['courses_taught'] ?>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-lg font-weight-bold">
                                    <?= (int)$data['score'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $score = (int)$data['score'];
                                if ($score >= 80) {
                                    echo '<span class="badge badge-success">Baik</span>';
                                } elseif ($score >= 60) {
                                    echo '<span class="badge badge-warning">Cukup</span>';
                                } else {
                                    echo '<span class="badge badge-danger">Kurang</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Data</h5>
                                <p class="text-muted">Data integritas untuk semester ini belum tersedia.</p>
                                <a href="<?= base_url('upload') ?>" class="btn btn-primary">
                                    <i class="fas fa-upload mr-1"></i>
                                    Upload Data
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($integrityData)): ?>
        <div class="card-footer">
            <?= view('components/table_info', [
                'total' => count($integrityData),
                'type' => 'dosen'
            ]) ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .sortable {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s ease;
    }

    .sortable:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sort-icon {
        font-size: 0.8em;
        opacity: 0.6;
        transition: opacity 0.3s ease, color 0.3s ease;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    .sortable.sort-asc .sort-icon::before {
        content: "\f0de";
        /* fa-sort-up */
        color: #28a745;
        opacity: 1;
    }

    .sortable.sort-desc .sort-icon::before {
        content: "\f0dd";
        /* fa-sort-down */
        color: #dc3545;
        opacity: 1;
    }

    .sortable.sort-asc .sort-icon,
    .sortable.sort-desc .sort-icon {
        opacity: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('integrityTable');
        const headers = table.querySelectorAll('th.sortable');
        const tbody = table.querySelector('tbody');
        let currentSort = {
            column: 'name',
            order: 'asc'
        };

        function sortTable(column, order) {
            const dirModifier = order === 'asc' ? 1 : -1;
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Sort rows
            rows.sort((a, b) => {
                const aText = a.querySelector(`[data-sort="${column}"]`)?.innerText.trim() || '';
                const bText = b.querySelector(`[data-sort="${column}"]`)?.innerText.trim() || '';

                return aText.localeCompare(bText, undefined, {
                    numeric: true
                }) * dirModifier;
            });

            // Remove existing rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }

            // Re-add sorted rows
            tbody.append(...rows);
        }

        headers.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                let order = 'asc';

                // Determine new order
                if (currentSort.column === column && currentSort.order === 'asc') {
                    order = 'desc';
                }

                currentSort = {
                    column,
                    order
                };

                // Sort table
                sortTable(column, order);

                // Update sort icons
                headers.forEach(h => {
                    const icon = h.querySelector('.sort-icon');
                    if (h === header) {
                        h.classList.toggle('sort-asc', order === 'asc');
                        h.classList.toggle('sort-desc', order === 'desc');
                    } else {
                        h.classList.remove('sort-asc', 'sort-desc');
                    }
                });
            });
        });

        // Initial sort
        sortTable(currentSort.column, currentSort.order);
    });
</script>