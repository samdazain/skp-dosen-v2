<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Dashboard SKP Dosen',
    'breadcrumbs' => [
        ['text' => 'Home', 'url' => 'dashboard'],
        ['text' => 'Dashboard', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Display Success Alert -->
        <?php if (session()->get('success')): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5><i class="icon fas fa-check-circle"></i> Berhasil!</h5>
                        <?= esc(session()->get('success')) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Display Error Alert -->
        <?php if (session()->get('error')): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                        <?= esc(session()->get('error')) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Display Upload Results with Details -->
        <?php if (session()->get('errors')): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Detail Error Upload:</h5>
                        <div class="error-details" style="max-height: 300px; overflow-y: auto;">
                            <ul class="mb-0">
                                <?php foreach (session()->get('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Silakan periksa format data pada file Excel dan coba upload ulang untuk data yang gagal.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info boxes -->
        <div class="row">
            <?= view('dashboard/partials/info_box', [
                'icon' => 'fas fa-users',
                'bg_color' => 'bg-primary',
                'title' => 'Jumlah Dosen',
                'value' => $totalLecturers ?? '0',
                'description' => 'Terdaftar dalam sistem'
            ]) ?>
        </div>

        <!-- Main Cards -->
        <div class="row">
            <!-- Upload Data Cards -->
            <?= view('dashboard/partials/upload_card', [
                'title' => 'Upload Data Dosen',
                'icon' => 'fas fa-upload',
                'card_class' => 'card-primary',
                'upload_url' => 'upload/dosen',
                'input_id' => 'fileDataDosen',
                'input_name' => 'file_dosen',
                'arrow_color' => 'text-primary',
                'button_class' => 'btn-primary',
                'download_url' => base_url('assets/templates/template_dosen.xlsx')
            ]) ?>

            <?= view('dashboard/partials/upload_card', [
                'title' => 'Upload Data Integritas',
                'icon' => 'fas fa-shield-alt',
                'card_class' => 'card-success',
                'upload_url' => 'upload/integritas',
                'input_id' => 'fileIntegritas',
                'input_name' => 'file_integritas',
                'arrow_color' => 'text-success',
                'button_class' => 'btn-success',
                'download_url' => base_url('assets/templates/template_integritas.xlsx')
            ]) ?>

            <?= view('dashboard/partials/upload_card', [
                'title' => 'Upload Data Disiplin',
                'icon' => 'fas fa-tasks',
                'card_class' => 'card-info',
                'upload_url' => 'upload/disiplin',
                'input_id' => 'fileDisiplin',
                'input_name' => 'file_disiplin',
                'arrow_color' => 'text-info',
                'button_class' => 'btn-info',
                'download_url' => base_url('assets/templates/template_disiplin.xlsx')
            ]) ?>

            <?= view('dashboard/partials/upload_card', [
                'title' => 'Upload Data Orientasi Pelayanan',
                'icon' => 'fas fa-concierge-bell',
                'card_class' => 'card-warning',
                'upload_url' => 'upload/pelayanan',
                'input_id' => 'filePelayanan',
                'input_name' => 'file_pelayanan',
                'arrow_color' => 'text-warning',
                'button_class' => 'btn-warning text-white',
                'download_url' => base_url('assets/templates/template_pelayanan.xlsx')
            ]) ?>

            <!-- Action Cards -->
            <?= view('dashboard/partials/action_card', [
                'title' => 'Penilaian Komitmen',
                'icon' => 'fas fa-handshake',
                'card_class' => 'card-danger',
                'icon_color' => 'text-danger',
                'description' => 'Kelola dan lakukan penilaian komitmen dosen',
                'action_url' => 'commitment',
                'button_class' => 'btn-danger',
                'button_text' => 'Buka Penilaian'
            ]) ?>

            <?= view('dashboard/partials/action_card', [
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
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Add smooth animation to alerts
        $('.alert').addClass('animated fadeIn');
    });
</script>
<?= $this->endSection() ?>