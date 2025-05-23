<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Tambah Pengguna',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Manajemen Pengguna', 'url' => 'user'],
        ['text' => 'Tambah Pengguna', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?= view('user/partials/form', [
                    'isEdit' => false,
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

        // Handle role change
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

        // NIP validation - only allow digits
        const nipField = document.getElementById('nip');
        nipField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Password confirmation validation
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

        // Add form submission debugging
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            console.log('Form being submitted...');

            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            // Don't prevent default - let the form submit normally
        });
    });
</script>
<?= $this->endSection() ?>