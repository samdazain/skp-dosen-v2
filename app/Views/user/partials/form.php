<?php

/**
 * User form partial
 * 
 * @var array|null $userData User data for edit mode
 * @var array $studyPrograms Study programs list
 * @var bool $isEdit Whether form is in edit mode
 */

// No need to load helpers here as they should be loaded in the controller
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?= $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?></h3>
    </div>

    <?= form_open($isEdit ? site_url('user/update/' . $userData['id']) : site_url('user/store'), ['method' => 'post', 'autocomplete' => 'off']) ?>
    <div class="card-body">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <?= view_cell('\App\Libraries\AlertLibrary::displayErrors', ['errors' => session('errors')]) ?>
            </div>
        <?php endif; ?>

        <!-- Name Field -->
        <div class="form-group">
            <?= form_label('Nama Lengkap <span class="text-danger">*</span>', 'name', ['class' => 'form-label']) ?>
            <?= form_input([
                'name' => 'name',
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Masukkan nama lengkap',
                'value' => old('name', $userData['name'] ?? ''),
                'required' => true
            ]) ?>
        </div>

        <!-- NIP Field -->
        <div class="form-group">
            <?= form_label('NIP <span class="text-danger">*</span>', 'nip', ['class' => 'form-label']) ?>
            <?= form_input([
                'type' => 'text',
                'name' => 'nip',
                'id' => 'nip',
                'class' => 'form-control',
                'placeholder' => 'Masukkan NIP',
                'value' => old('nip', $userData['nip'] ?? ''),
                'required' => true,
                'pattern' => '[0-9]*',
                'title' => 'NIP harus berisi angka saja',
                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"
            ]) ?>
            <small class="form-text text-muted">Format: Angka saja</small>
        </div>

        <!-- Email Field -->
        <div class="form-group">
            <?= form_label('Email <span class="text-danger">*</span>', 'email', ['class' => 'form-label']) ?>
            <?= form_input([
                'type' => 'email',
                'name' => 'email',
                'id' => 'email',
                'class' => 'form-control',
                'placeholder' => 'Masukkan alamat email',
                'value' => old('email', $userData['email'] ?? ''),
                'required' => true
            ]) ?>
        </div>

        <!-- Position Field -->
        <div class="form-group">
            <?= form_label('Jabatan <span class="text-danger">*</span>', 'position', ['class' => 'form-label']) ?>
            <?= form_input([
                'name' => 'position',
                'id' => 'position',
                'class' => 'form-control',
                'placeholder' => 'Masukkan jabatan',
                'value' => old('position', $userData['position'] ?? ''),
                'required' => true
            ]) ?>
        </div>

        <!-- Role Field -->
        <div class="form-group">
            <?= form_label('Role <span class="text-danger">*</span>', 'role', ['class' => 'form-label']) ?>
            <?= form_dropdown(
                'role',
                ['' => '-- Pilih Role --'] + get_role_list(),
                old('role', $userData['role'] ?? ''),
                ['class' => 'form-control', 'id' => 'role', 'required' => true]
            ) ?>
        </div>

        <!-- Study Program Field (conditional) -->
        <?php
        $displayStyle = (old('role', $userData['role'] ?? '') == 'kaprodi') ? 'block' : 'none';
        $studyProgramOptions = ['' => '-- Pilih Program Studi --'];
        foreach ($studyPrograms as $program) {
            $studyProgramOptions[$program] = $program;
        }
        ?>
        <div class="form-group" id="study-program-group" style="display: <?= $displayStyle ?>;">
            <?= form_label('Program Studi <span class="text-danger">*</span>', 'study_program', ['class' => 'form-label']) ?>
            <?= form_dropdown(
                'study_program',
                $studyProgramOptions,
                old('study_program', $userData['study_program'] ?? ''),
                ['class' => 'form-control', 'id' => 'study_program']
            ) ?>
        </div>

        <?php if ($isEdit): ?>
            <hr>
            <h5>Ubah Password</h5>
            <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>

            <!-- Password Field (for edit) -->
            <div class="form-group">
                <?= form_label('Password Baru', 'password', ['class' => 'form-label']) ?>
                <?= form_password([
                    'name' => 'password',
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => 'Masukkan password baru',
                    'minlength' => 6
                ]) ?>
                <small class="form-text text-muted">Minimal 6 karakter, harus mengandung minimal satu huruf dan satu
                    angka</small>
            </div>

            <!-- Password Confirmation Field (for edit) -->
            <div class="form-group">
                <?= form_label('Konfirmasi Password Baru', 'password_confirm', ['class' => 'form-label']) ?>
                <?= form_password([
                    'name' => 'password_confirm',
                    'id' => 'password_confirm',
                    'class' => 'form-control',
                    'placeholder' => 'Masukkan kembali password baru'
                ]) ?>
            </div>
        <?php else: ?>
            <!-- Password Field (for new user) -->
            <div class="form-group">
                <?= form_label('Password <span class="text-danger">*</span>', 'password', ['class' => 'form-label']) ?>
                <?= form_password([
                    'name' => 'password',
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => 'Masukkan password',
                    'required' => true,
                    'minlength' => 6
                ]) ?>
                <small class="form-text text-muted">Minimal 6 karakter, harus mengandung minimal satu huruf dan satu
                    angka</small>
            </div>

            <!-- Password Confirmation Field (for new user) -->
            <div class="form-group">
                <?= form_label('Konfirmasi Password <span class="text-danger">*</span>', 'password_confirm', ['class' => 'form-label']) ?>
                <?= form_password([
                    'name' => 'password_confirm',
                    'id' => 'password_confirm',
                    'class' => 'form-control',
                    'placeholder' => 'Masukkan kembali password',
                    'required' => true
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-footer">
        <?= form_submit('submit', $isEdit ? 'Simpan Perubahan' : 'Simpan', ['class' => 'btn btn-primary']) ?>
        <a href="<?= site_url('user') ?>" class="btn btn-default">Batal</a>
    </div>
    <?= form_close() ?>
</div>