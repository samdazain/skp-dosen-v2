<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'title' => 'Edit Pengguna',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Manajemen Pengguna', 'url' => 'user'],
        ['text' => 'Edit Pengguna', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?= view('user/partials/user_form', [
                    'formTitle' => 'Form Edit Pengguna',
                    'actionUrl' => base_url('user/update/' . $userData['id']),
                    'submitButtonText' => 'Simpan Perubahan',
                    'userData' => $userData,
                    'studyPrograms' => $studyPrograms,
                    'isEdit' => true
                ]) ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user/form.js') ?>"></script>
<?= $this->endSection() ?>