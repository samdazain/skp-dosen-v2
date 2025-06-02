<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Daftar Dosen',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Daftar Dosen', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?= view('components/alerts') ?>

        <div class="row">
            <div class="col-12">
                <?= view('lecturers/partials/lecturer_table', [
                    'lecturers' => $lecturers,
                    'pagination' => $pagination ?? [],
                    'search' => $search ?? '',
                    'total' => $total ?? 0,
                    'sortBy' => $sortBy ?? 'position',
                    'sortOrder' => $sortOrder ?? 'asc',
                    'perPage' => $perPage ?? 10
                ]) ?>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi Hapus</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus dosen <strong id="deleteLecturerName"></strong>?</p>
                <p class="text-warning"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteLecturerName').textContent = name;
        document.getElementById('deleteForm').action = '<?= base_url('lecturers/delete/') ?>' + id;
        $('#confirmDeleteModal').modal('show');
    }

    // Enhanced Lecturer Table Manager
    document.addEventListener('DOMContentLoaded', function() {
        const lecturerTable = new LecturerTableManager();
        lecturerTable.init();
    });

    class LecturerTableManager {
        constructor() {
            this.searchForm = document.getElementById('searchForm');
            this.searchInput = document.getElementById('searchInput');
            this.sortableHeaders = document.querySelectorAll('.sortable');
            this.currentSort = {
                by: '<?= $sortBy ?>',
                order: '<?= $sortOrder ?>'
            };
        }

        init() {
            this.initializeSearch();
            this.initializeSorting();
        }

        initializeSearch() {
            if (!this.searchInput || !this.searchForm) return;

            // Real-time search with debounce
            let searchTimeout;
            this.searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch();
                }, 500);
            });

            // Handle form submission
            this.searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }

        initializeSorting() {
            this.sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const sortBy = header.dataset.sort;
                    const sortOrder = this.currentSort.by === sortBy && this.currentSort.order ===
                        'asc' ? 'desc' : 'asc';
                    this.performSort(sortBy, sortOrder);
                });

                // Update header UI based on current sort
                this.updateSortHeader(header);
            });
        }

        performSearch() {
            const searchTerm = this.searchInput.value.trim();
            const url = new URL(window.location.href);

            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }

            // Reset to first page when searching
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        performSort(sortBy, sortOrder) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_order', sortOrder);

            // Reset to first page when sorting
            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        updateSortHeader(header) {
            const sortBy = header.dataset.sort;

            // Remove existing sort classes
            header.classList.remove('sort-asc', 'sort-desc');

            // Add appropriate class if this is the current sort column
            if (this.currentSort.by === sortBy) {
                header.classList.add(this.currentSort.order === 'asc' ? 'sort-asc' : 'sort-desc');
            }
        }
    }
</script>

<style>
    .sortable {
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    .sortable:hover {
        background-color: #f8f9fa;
    }

    .sortable::after {
        content: '\f0dc';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 8px;
        opacity: 0.3;
    }

    .sortable.sort-asc::after {
        content: '\f0de';
        opacity: 1;
        color: #007bff;
    }

    .sortable.sort-desc::after {
        content: '\f0dd';
        opacity: 1;
        color: #007bff;
    }
</style>
<?= $this->endSection() ?>