<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header with Breadcrumbs -->
<?= view('components/content_header', [
    'header_title' => 'Data Disiplin Dosen',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Data Disiplin', 'active' => true]
    ],
    'show_semester_selector' => false
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Alert Messages -->
        <?= view('components/alert_messages') ?>

        <!-- Semester Info -->
        <?= view('components/semester_info') ?>

        <div class="row">
            <div class="col-12">
                <!-- Discipline Data Table -->
                <?= view('discipline/partials/discipline_table') ?>

                <!-- Statistics Cards -->
                <?= view('discipline/partials/statistics_cards') ?>
            </div>
        </div>
    </div>
</section>

<script>
    // Main search functionality - enhanced to work with sorting
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('#disciplineTable tbody tr');

                tableRows.forEach(row => {
                    // Skip rows that don't have lecturer data (like "no data" message)
                    if (row.cells.length < 6) {
                        return;
                    }

                    const name = row.cells[1].textContent.toLowerCase();
                    const nip = row.cells[1].textContent.toLowerCase(); // NIP is in the name cell
                    const position = row.dataset.position?.toLowerCase() || '';
                    const studyProgram = row.cells[2].textContent.toLowerCase();

                    if (name.includes(searchTerm) ||
                        nip.includes(searchTerm) ||
                        position.includes(searchTerm) ||
                        studyProgram.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update table info after search
                updateTableInfo();
            });
        }

        function updateTableInfo() {
            const visibleRows = document.querySelectorAll(
                '#disciplineTable tbody tr[style=""], #disciplineTable tbody tr:not([style])');
            const emptyRows = document.querySelectorAll('#disciplineTable tbody tr td[colspan]');
            const actualRows = visibleRows.length - emptyRows.length;

            const tableInfo = document.querySelector('.card-footer .float-left small');
            if (tableInfo && actualRows >= 0) {
                tableInfo.innerHTML = `
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan ${actualRows} data dosen.
                    Data disiplin dikelola melalui upload Excel.
                    Nilai dihitung otomatis berdasarkan konfigurasi scoring.
                `;
            }
        }
    });
</script>

<?= $this->endSection() ?>