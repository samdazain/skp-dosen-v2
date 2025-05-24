<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Edit Dosen',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Daftar Dosen', 'url' => 'lecturers'],
        ['text' => 'Edit Dosen', 'active' => true]
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
                        <h3 class="card-title">Form Edit Dosen</h3>
                    </div>

                    <?= form_open('lecturers/update/' . $lecturer['id']) ?>
                    <input type="hidden" name="_method" value="PUT">
                    <?= csrf_field() ?>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="nip">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.nip') ? 'is-invalid' : '' ?>"
                                id="nip" name="nip" value="<?= old('nip', $lecturer['nip']) ?>"
                                placeholder="Masukkan NIP" required>
                            <?php if (session('errors.nip')) : ?>
                                <div class="invalid-feedback"><?= session('errors.nip') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                                id="name" name="name" value="<?= old('name', $lecturer['name']) ?>"
                                placeholder="Masukkan nama lengkap" required>
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback"><?= session('errors.name') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="position">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.position') ? 'is-invalid' : '' ?>"
                                id="position" name="position" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <?php foreach ($positions as $position) : ?>
                                    <option value="<?= $position ?>"
                                        <?= old('position', $lecturer['position']) == $position ? 'selected' : '' ?>>
                                        <?= $position ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.position')) : ?>
                                <div class="invalid-feedback"><?= session('errors.position') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group" id="study_program_container">
                            <label for="study_program">Program Studi</label>
                            <select class="form-control <?= session('errors.study_program') ? 'is-invalid' : '' ?>"
                                id="study_program" name="study_program" <?= $isLeadershipPosition ? 'disabled' : '' ?>>
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($studyPrograms as $key => $value) : ?>
                                    <option value="<?= $key ?>"
                                        <?= old('study_program', $lecturer['study_program']) == $key ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.study_program')) : ?>
                                <div class="invalid-feedback"><?= session('errors.study_program') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted study-program-note">
                                Wajib diisi kecuali Dekanat
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('lecturers') ?>" class="btn btn-secondary">Batal</a>
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
        const positionSelect = document.getElementById('position');
        const studyProgramContainer = document.getElementById('study_program_container');
        const studyProgramSelect = document.getElementById('study_program');

        // Leadership positions that don't require study program
        const leadershipPositions = ['DEKAN', 'WAKIL DEKAN I', 'WAKIL DEKAN II', 'WAKIL DEKAN III'];

        // Handle position change
        positionSelect.addEventListener('change', function() {
            const selectedPosition = this.value;
            const isLeadership = leadershipPositions.includes(selectedPosition);

            if (isLeadership) {
                // Disable study program for leadership positions
                studyProgramSelect.disabled = true;
                studyProgramSelect.value = '';
                studyProgramSelect.removeAttribute('required');
                studyProgramContainer.classList.add('text-muted');
            } else {
                // Enable study program for other positions
                studyProgramSelect.disabled = false;
                studyProgramSelect.setAttribute('required', 'required');
                studyProgramContainer.classList.remove('text-muted');
            }
        });

        // Initialize state based on any pre-selected value
        positionSelect.dispatchEvent(new Event('change'));
    });
</script>
<?= $this->endSection() ?>