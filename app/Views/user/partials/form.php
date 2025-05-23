<?php

/**
 * User form partial
 * 
 * @var array $userData User data for edit mode
 * @var array $studyPrograms Study programs list
 * @var bool $isEdit Whether form is in edit mode
 */

// For loading role helper
helper('role');
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?= $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?></h3>
    </div>
    <form action="<?= $isEdit ? base_url('user/update/' . $userData['id']) : base_url('user/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap"
                    value="<?= old('name', $userData['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="nip">NIP <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP"
                    value="<?= old('nip', $userData['nip'] ?? '') ?>" required pattern="[0-9]*"
                    title="NIP harus berisi angka saja" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <small class="form-text text-muted">Format: Angka saja</small>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan alamat email"
                    value="<?= old('email', $userData['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="position">Jabatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="position" name="position" placeholder="Masukkan jabatan"
                    value="<?= old('position', $userData['position'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role <span class="text-danger">*</span></label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <?php
                    $roles = get_role_list();
                    $selectedRole = old('role', $userData['role'] ?? '');

                    foreach ($roles as $value => $label):
                    ?>
                        <option value="<?= $value ?>" <?= ($selectedRole == $value) ? 'selected' : '' ?>><?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="study-program-group"
                style="display: <?= (old('role', $userData['role'] ?? '') == 'kaprodi') ? 'block' : 'none' ?>;">
                <label for="study_program">Program Studi <span class="text-danger">*</span></label>
                <select class="form-control" id="study_program" name="study_program">
                    <option value="">-- Pilih Program Studi --</option>
                    <?php
                    $selectedProgram = old('study_program', $userData['study_program'] ?? '');

                    foreach ($studyPrograms as $program):
                    ?>
                        <option value="<?= $program ?>" <?= ($selectedProgram == $program) ? 'selected' : '' ?>>
                            <?= $program ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($isEdit): ?>
                <hr>
                <h5>Ubah Password</h5>
                <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>

                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password baru" minlength="6">
                    <small class="form-text text-muted">Minimal 6 karakter, harus mengandung minimal satu huruf dan satu
                        angka</small>
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
                    <small class="form-text text-muted">Minimal 6 karakter, harus mengandung minimal satu huruf dan satu
                        angka</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                        placeholder="Masukkan kembali password" required>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan' ?></button>
            <a href="<?= base_url('user') ?>" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>