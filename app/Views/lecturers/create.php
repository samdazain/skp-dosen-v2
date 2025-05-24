<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tambah Dosen Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('lecturers') ?>">Daftar Dosen</a></li>
                    <li class="breadcrumb-item active">Tambah Dosen</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah Dosen</h3>
                    </div>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                    <li><?= esc($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?= form_open('lecturers/store', ['id' => 'lecturerForm']) ?>
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Masukkan nama lengkap dosen" required>
                        </div>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="Masukkan NIP dosen"
                                required>
                            <small class="form-text text-muted">Format: 18 digit angka</small>
                        </div>
                        <div class="form-group">
                            <label for="position">Jabatan</label>
                            <select class="form-control" id="position" name="position" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <?php foreach ($positions as $position): ?>
                                    <option value="<?= $position ?>"><?= $position ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="study_program_container">
                            <label for="study_program">Program Studi</label>
                            <select class="form-control" id="study_program" name="study_program">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($studyPrograms as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted study-program-note">
                                Wajib diisi kecuali Dekanat
                            </small>
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