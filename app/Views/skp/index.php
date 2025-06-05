<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Data Master SKP',
    'breadcrumbs' => [
        ['text' => 'Home', 'url' => 'skp'],
        ['text' => 'SKP', 'active' => true]
    ]
]) ?>

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



        <!-- Filters -->
        <?= view('skp/partials/filters') ?>

        <div class="row">
            <div class="col-12">
                <!-- Data Table -->
                <?= view('skp/partials/table') ?>

                <!-- Statistics -->
                <?= view('skp/partials/statistics') ?>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<?= view('skp/partials/scripts') ?>

<!-- Custom CSS for SKP display -->
<style>
    .skp-score-display {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .skp-score-display .badge {
        min-width: 60px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .component-score {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    /* Category badges */
    .badge-sangat-baik {
        background-color: #28a745 !important;
        color: white !important;
    }

    .badge-baik {
        background-color: #007bff !important;
        color: white !important;
    }

    .badge-cukup {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-kurang {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .badge-belum-dinilai {
        background-color: #6c757d !important;
        color: white !important;
    }

    /* Table enhancements */
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .table .badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.5rem;
    }
</style>

<?= $this->endSection() ?>