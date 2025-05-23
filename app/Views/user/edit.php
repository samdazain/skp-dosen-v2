<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Edit Pengguna',
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
                <?= view('user/partials/form', [
                    'isEdit' => true,
                    'userData' => $userData,
                    'studyPrograms' => $studyPrograms
                ]) ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const studyProgramGroup = document.getElementById('study-program-group');
        const studyProgramSelect = document.getElementById('study_program');
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirm');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'kaprodi') {
                studyProgramGroup.style.display = 'block';
                studyProgramSelect.setAttribute('required', 'required');
            } else {
                studyProgramGroup.style.display = 'none';
                studyProgramSelect.removeAttribute('required');
                studyProgramSelect.value = '';
            }
        });

        // Add password matching validation for edit form too
        if (passwordField && confirmField) {
            confirmField.addEventListener('input', function() {
                if (this.value === passwordField.value) {
                    this.setCustomValidity('');
                } else {
                    this.setCustomValidity('Password tidak cocok');
                }
            });

            passwordField.addEventListener('input', function() {
                if (confirmField.value) {
                    if (confirmField.value === this.value) {
                        confirmField.setCustomValidity('');
                    } else {
                        confirmField.setCustomValidity('Password tidak cocok');
                    }
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>