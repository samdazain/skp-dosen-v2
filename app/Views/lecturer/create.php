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
                    <form action="<?= base_url('lecturers/store') ?>" method="post">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan nama lengkap dosen" required>
                            </div>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip"
                                    placeholder="Masukkan NIP dosen" required>
                                <small class="form-text text-muted">Format: 18 digit angka</small>
                            </div>
                            <div class="form-group">
                                <label for="study_program">Program Studi</label>
                                <select class="form-control" id="study_program" name="study_program" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    <option value="informatika">Informatika</option>
                                    <option value="sistem_informasi">Sistem Informasi</option>
                                    <option value="sains_data">Sains Data</option>
                                    <option value="bisnis_digital">Bisnis Digital</option>
                                    <option value="magister_teknologi_informasi">Magister Teknologi Informasi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="position">Jabatan</label>
                                <select class="form-control" id="position" name="position" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="asisten_ahli">Asisten Ahli</option>
                                    <option value="lektor">Lektor</option>
                                    <option value="lektor_kepala">Lektor Kepala</option>
                                    <option value="guru_besar">Guru Besar</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('lecturers') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary float-right">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>