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
                <?= view('lecturer/partials/lecturer_table', ['lecturers' => $lecturers]) ?>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<?= view('components/confirm_delete_modal', [
    'title' => 'Konfirmasi Hapus',
    'message' => 'Anda yakin ingin menghapus data dosen ini?'
]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/lecturer/index.js') ?>"></script>
<?= $this->endSection() ?>