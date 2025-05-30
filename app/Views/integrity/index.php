<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<?= view('integrity/partials/header') ?>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Messages -->
        <?= view('components/alerts') ?>

        <!-- Data Table Card -->
        <?= view('integrity/partials/data_table', [
            'integrityData' => $integrityData ?? [],
            'currentSemester' => $currentSemester ?? null
        ]) ?>

        <!-- Statistics Cards -->
        <?= view('integrity/partials/statistics', [
            'statistics' => $statistics ?? []
        ]) ?>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= view('integrity/partials/scripts') ?>
<?= $this->endSection() ?>