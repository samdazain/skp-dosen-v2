<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Orientasi Pelayanan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orientasi Pelayanan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

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
        <?= view('orientation/partials/filters') ?>

        <div class="row">
            <div class="col-12">
                <!-- Data Table -->
                <?= view('orientation/partials/table') ?>

                <!-- Statistics -->
                <?= view('orientation/partials/statistics') ?>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<?= view('orientation/partials/scripts') ?>

<!-- Custom CSS for readonly interface -->
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

<?= $this->endSection() ?>