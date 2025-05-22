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
                <h1 class="m-0">Daftar Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Daftar Dosen</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Dosen Fakultas</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('lecturers/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> Tambah Dosen
                            </a>
                            <div class="input-group input-group-sm ml-3 pt-1 float-right" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" id="searchInput"
                                    placeholder="Cari dosen...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Dosen</th>
                                    <th>NIP</th>
                                    <th class="text-center">Program Studi</th>
                                    <th class="text-center">Jabatan</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Dummy data untuk demonstrasi
                                $dummyData = [
                                    [
                                        'name' => 'Dr. Budi Santoso, M.Kom',
                                        'nip' => '197505152000121001',
                                        'study_program' => 'Informatika',
                                        'position' => 'Lektor Kepala'
                                    ],
                                    [
                                        'name' => 'Dr. Siti Rahayu, M.Sc',
                                        'nip' => '198003212005012002',
                                        'study_program' => 'Sistem Informasi',
                                        'position' => 'Lektor'
                                    ],
                                    [
                                        'name' => 'Prof. Dr. Agus Wijaya, M.T',
                                        'nip' => '196812101995121001',
                                        'study_program' => 'Magister Teknologi Informasi',
                                        'position' => 'Guru Besar'
                                    ],
                                    [
                                        'name' => 'Dewi Lestari, S.Kom., M.Cs',
                                        'nip' => '198907182015042003',
                                        'study_program' => 'Sains Data',
                                        'position' => 'Asisten Ahli'
                                    ],
                                    [
                                        'name' => 'Dr. Ahmad Fauzi, M.M',
                                        'nip' => '197303052001121003',
                                        'study_program' => 'Bisnis Digital',
                                        'position' => 'Lektor'
                                    ],
                                    [
                                        'name' => 'Dr. Rina Fitriani, M.Kom',
                                        'nip' => '198505122010122005',
                                        'study_program' => 'Informatika',
                                        'position' => 'Lektor'
                                    ],
                                    [
                                        'name' => 'Dr. Hendra Gunawan, M.T',
                                        'nip' => '197202182000031001',
                                        'study_program' => 'Sistem Informasi',
                                        'position' => 'Lektor Kepala'
                                    ]
                                ];

                                foreach ($dummyData as $i => $lecturer):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td><?= $lecturer['nip'] ?></td>
                                        <td class="text-center"><?= $lecturer['study_program'] ?></td>
                                        <td class="text-center"><?= $lecturer['position'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?= base_url('lecturers/edit/' . ($i + 1)) ?>"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger" title="Delete"
                                                    onclick="confirmDelete(<?= $i + 1 ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus data dosen ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/lecturer/index.js') ?>"></script>
<?= $this->endSection() ?>