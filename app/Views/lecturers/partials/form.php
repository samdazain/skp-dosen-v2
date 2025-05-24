<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Tambah Dosen',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Daftar Dosen', 'url' => 'lecturers'],
        ['text' => 'Tambah Dosen', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?= view('components/alerts') ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah Dosen</h3>
                    </div>

                    <?= form_open('lecturers/store') ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nip">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nip') ? 'is-invalid' : '' ?>"
                                id="nip" name="nip" value="<?= old('nip') ?>" placeholder="Masukkan NIP">
                            <?php if (session('errors.nip')) : ?>
                            <div class="invalid-feedback"><?= session('errors.nip') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                                id="name" name="name" value="<?= old('name') ?>" placeholder="Masukkan nama lengkap">
                            <?php if (session('errors.name')) : ?>
                            <div class="invalid-feedback"><?= session('errors.name') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                id="email" name="email" value="<?= old('email') ?>" placeholder="Masukkan email">
                            <?php if (session('errors.email')) : ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="study_program">Program Studi <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.study_program') ? 'is-invalid' : '' ?>"
                                id="study_program" name="study_program">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($studyPrograms as $key => $value) : ?>
                                <option value="<?= $key ?>" <?= old('study_program') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.study_program')) : ?>
                            <div class="invalid-feedback"><?= session('errors.study_program') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('lecturers') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>