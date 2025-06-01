<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<?= view('cooperation/partials/header') ?>

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

        <?php if (session()->has('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= session('warning') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= session('info') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <?= view('cooperation/partials/filters', [
            'filters' => $filters ?? []
        ]) ?>

        <div class="row">
            <div class="col-12">
                <!-- Data Table -->
                <?= view('cooperation/partials/table', [
                    'lecturersData' => $lecturersData ?? [],
                    'currentSemester' => $currentSemester ?? null,
                    'autoPopulationResult' => $autoPopulationResult ?? ['added' => 0]
                ]) ?>

                <!-- Statistics -->
                <?= view('cooperation/partials/statistics', [
                    'stats' => $stats ?? [],
                    'lecturersData' => $lecturersData ?? []
                ]) ?>
            </div>
        </div>

        <!-- Auto-refresh notification -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Info:</strong> Data kerja sama dosen diperbarui secara otomatis dari tabel dosen.
            Semua dosen akan muncul dengan status default yang dapat Anda ubah sesuai kebutuhan.
            <?php if (isset($autoPopulationResult) && $autoPopulationResult['added'] > 0): ?>
                <br><small class="text-success">
                    <i class="fas fa-check mr-1"></i>
                    Baru saja menambahkan <?= $autoPopulationResult['added'] ?> record kerja sama baru pada
                    <?= $autoPopulationResult['timestamp'] ?>
                </small>
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</section>

<!-- Modals -->
<?= view('cooperation/partials/modals') ?>

<!-- Scripts -->
<?= view('cooperation/partials/scripts') ?>

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

    .btn-outline-primary.active,
    .btn-outline-primary:active {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .btn-outline-info.active,
    .btn-outline-info:active {
        background-color: #17a2b8 !important;
        border-color: #17a2b8 !important;
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

    .progress-group {
        margin-bottom: 0.5rem;
    }

    .progress-text {
        font-size: 0.875rem;
        font-weight: 500;
    }

    .table-active {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }

    .badge-light {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    .card-tools .btn-tool {
        color: #6c757d;
    }

    .card-tools .btn-tool:hover {
        color: #495057;
    }

    /* Ensure filters are always visible */
    .card-body {
        display: block !important;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-control {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .btn-group .btn {
        margin-right: 0.25rem;
    }
</style>

<?= $this->endSection() ?>