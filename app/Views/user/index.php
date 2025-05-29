<?php

/**
 * @var CodeIgniter\View\View $this
 */
helper('navigation');
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Manajemen Pengguna',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Manajemen Pengguna', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?= view('components/alerts') ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengguna Sistem</h3>

                <div class="card-tools">
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="<?= base_url('user/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pengguna
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="<?= base_url('user') ?>" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari pengguna..." name="search"
                                    value="<?= $search ?? '' ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="<?= base_url('user') ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Email</th>
                                <th>Role</th>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <th style="width: 10%">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="<?= ($user['role'] === 'admin') ? 8 : 7 ?>" class="text-center">Tidak ada
                                        data pengguna.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $i => $userData): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= $userData['name'] ?></td>
                                        <td><?= $userData['nip'] ?></td>
                                        <td><?= $userData['position'] ?></td>
                                        <td><?= $userData['email'] ?></td>
                                        <td><?= get_role_badge($userData['role']) ?></td>
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('user/edit/' . $userData['id']) ?>" class="btn btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="<?= $userData['id'] ?>"
                                                        data-name="<?= $userData['name'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($user['role'] === 'admin'): ?>
    <!-- Delete Confirmation Modal -->
    <?= view('components/delete_modal_user', [
        'title' => 'Konfirmasi Hapus',
        'messagePrefix' => 'Apakah Anda yakin ingin menghapus pengguna'
    ]) ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/user/index.js') ?>"></script>
<?= $this->endSection() ?>