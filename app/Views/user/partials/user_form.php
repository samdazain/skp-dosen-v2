<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?= $formTitle ?? 'Form Pengguna' ?></h3>
    </div>

    <form action="<?= $actionUrl ?>" method="post">
        <div class="card-body">
            <?= view('components/validation_errors') ?>

            <div class="form-group">
                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap"
                    value="<?= old('name', $userData['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="nip">NIP <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP (18 digit)"
                    value="<?= old('nip', $userData['nip'] ?? '') ?>" required minlength="18" maxlength="18"
                    pattern="\d{18}" title="NIP harus terdiri dari 18 digit angka">
                <small class="form-text text-muted">Format: 18 digit angka</small>
            </div>

            <div class="form-group">
                <label for="position">Jabatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="position" name="position" placeholder="Masukkan jabatan"
                    value="<?= old('position', $userData['position'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan alamat email"
                    value="<?= old('email', $userData['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role <span class="text-danger">*</span></label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <?php
                    $roles = [
                        'admin' => 'Admin',
                        'dekan' => 'Dekan',
                        'wadek1' => 'Wakil Dekan 1',
                        'wadek2' => 'Wakil Dekan 2',
                        'wadek3' => 'Wakil Dekan 3',
                        'kaprodi' => 'Kaprodi',
                        'staff' => 'Staff'
                    ];

                    $selectedRole = old('role', $userData['role'] ?? '');

                    foreach ($roles as $value => $label):
                    ?>
                        <option value="<?= $value ?>" <?= ($selectedRole == $value) ? 'selected' : '' ?>><?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="study-program-group"
                style="display: <?= old('role', $userData['role'] ?? '') == 'kaprodi' ? 'block' : 'none' ?>;">
                <label for="study_program">Program Studi <span class="text-danger">*</span></label>
                <select class="form-control" id="study_program" name="study_program">
                    <option value="">-- Pilih Program Studi --</option>
                    <?php
                    $selectedProgram = old('study_program', $userData['study_program'] ?? '');

                    foreach ($studyPrograms as $program):
                    ?>
                        <option value="<?= $program ?>" <?= $selectedProgram == $program ? 'selected' : '' ?>>
                            <?= $program ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Wajib diisi jika role adalah Kaprodi</small>
            </div>

            <?php if (isset($isEdit) && $isEdit): ?>
                <hr>
                <h5>Ubah Password</h5>
                <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password baru" minlength="6">
                    <small class="form-text text-muted">Minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                        placeholder="Masukkan kembali password baru">
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password" required minlength="6">
                    <small class="form-text text-muted">Minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                        placeholder="Masukkan kembali password" required minlength="6">
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?= $submitButtonText ?? 'Simpan' ?></button>
            <a href="<?= base_url('user') ?>" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>