<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Ubah Password',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Ubah Password', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Password</h3>
                    </div>

                    <?= form_open(base_url('change-password')) ?>
                    <div class="card-body">
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger"><?= session('error') ?></div>
                        <?php endif; ?>

                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required
                                minlength="6">
                            <small class="form-text text-muted">Minimal 6 karakter, harus mengandung minimal satu huruf
                                dan satu angka</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        // Validate password strength
        newPassword.addEventListener('input', function() {
            const value = this.value;
            const hasLetter = /[A-Za-z]/.test(value);
            const hasNumber = /[0-9]/.test(value);
            const isLongEnough = value.length >= 6;

            if (value && (!hasLetter || !hasNumber || !isLongEnough)) {
                this.setCustomValidity(
                    'Password harus minimal 6 karakter, mengandung minimal satu huruf dan satu angka');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validate matching passwords
        confirmPassword.addEventListener('input', function() {
            if (this.value !== newPassword.value) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });

        // Update validation when new password changes
        newPassword.addEventListener('change', function() {
            if (confirmPassword.value) {
                if (confirmPassword.value !== this.value) {
                    confirmPassword.setCustomValidity('Password tidak cocok');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>