<?php
// Fix data extraction from controller
// The data is already available directly in the view scope, no need for this
// if (!empty($data)) extract($data);
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= $this->include('components/content_header', [
    'header_title' => 'Dashboard SKP Dosen',
    'breadcrumbs' => [
        ['text' => 'Home', 'url' => 'dashboard'],
        ['text' => 'Dashboard', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
            <?= $this->include('dashboard/partials/info_box', [
                'icon' => 'fas fa-users',
                'bg_color' => 'bg-primary',
                'title' => 'Jumlah Dosen',
                'value' => '42',
                'description' => 'Terdaftar dalam sistem'
            ]) ?>
        </div>

        <!-- Main Cards -->
        <div class="row">
            <!-- Upload Data Cards -->
            <?= view_cell('App\\Cells\\Dashboard::show', [
                'title' => 'Upload Data Dosen',
                'icon' => 'fas fa-upload',
                'card_class' => 'card-primary',
                'upload_url' => 'upload/dosen',
                'input_id' => 'fileDataDosen',
                'input_name' => 'file_dosen',
                'arrow_color' => 'text-primary',
                'button_class' => 'btn-primary',
                'download_url' => '#'
            ]) ?>

            <?= view_cell('App\Cells\Dashboard::show', [
                'title' => 'Upload Data Integritas',
                'icon' => 'fas fa-shield-alt',
                'card_class' => 'card-success',
                'upload_url' => 'upload/integritas',
                'input_id' => 'fileIntegritas',
                'input_name' => 'file_integritas',
                'arrow_color' => 'text-success',
                'button_class' => 'btn-success',
                'download_url' => '#'
            ]) ?>

            <?= view_cell('App\Cells\Dashboard::show', [
                'title' => 'Upload Data Disiplin',
                'icon' => 'fas fa-tasks',
                'card_class' => 'card-info',
                'upload_url' => 'upload/disiplin',
                'input_id' => 'fileDisiplin',
                'input_name' => 'file_disiplin',
                'arrow_color' => 'text-info',
                'button_class' => 'btn-info',
                'download_url' => '#'
            ]) ?>

            <?= view_cell('App\Cells\Dashboard::show', [
                'title' => 'Upload Data Orientasi Pelayanan',
                'icon' => 'fas fa-concierge-bell',
                'card_class' => 'card-warning',
                'upload_url' => 'upload/pelayanan',
                'input_id' => 'filePelayanan',
                'input_name' => 'file_pelayanan',
                'arrow_color' => 'text-warning',
                'button_class' => 'btn-warning text-white',
                'download_url' => '#'
            ]) ?>

            <!-- Action Cards -->
            <?= $this->include('dashboard/partials/action_card', [
                'title' => 'Penilaian Komitmen',
                'icon' => 'fas fa-handshake',
                'card_class' => 'card-danger',
                'icon_color' => 'text-danger',
                'description' => 'Kelola dan lakukan penilaian komitmen dosen',
                'action_url' => 'commitment',
                'button_class' => 'btn-danger',
                'button_text' => 'Buka Penilaian'
            ]) ?>

            <?= $this->include('dashboard/partials/action_card', [
                'title' => 'Penilaian Kerjasama',
                'icon' => 'fas fa-users',
                'card_class' => 'card-dark',
                'icon_color' => 'text-dark',
                'description' => 'Kelola dan lakukan penilaian kerjasama dosen',
                'action_url' => 'cooperation',
                'button_class' => 'btn-dark',
                'button_text' => 'Buka Penilaian'
            ]) ?>
        </div>

        <!-- Recent Activity -->
        <?= $this->include('dashboard/partials/activity_log', [
            'activities' => [
                [
                    'date' => date('d M Y H:i'),
                    'user' => 'Admin',
                    'activity' => 'Login ke sistem'
                ],
                [
                    'date' => date('d M Y H:i', strtotime('-1 day')),
                    'user' => 'Admin',
                    'activity' => 'Upload data integritas dosen'
                ],
                [
                    'date' => date('d M Y H:i', strtotime('-2 day')),
                    'user' => 'Admin',
                    'activity' => 'Memperbarui data dosen'
                ]
            ]
        ]) ?>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>