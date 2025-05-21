<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manajemen Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen Pengguna</li>
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
                                <th>Program Studi</th>
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
                                        <td>
                                            <?php
                                            $roleLabels = [
                                                'admin' => '<span class="badge badge-danger">Admin</span>',
                                                'dekan' => '<span class="badge badge-primary">Dekan</span>',
                                                'wadek1' => '<span class="badge badge-info">Wakil Dekan 1</span>',
                                                'wadek2' => '<span class="badge badge-info">Wakil Dekan 2</span>',
                                                'wadek3' => '<span class="badge badge-info">Wakil Dekan 3</span>',
                                                'kaprodi' => '<span class="badge badge-success">Kaprodi</span>',
                                                'staff' => '<span class="badge badge-secondary">Staff</span>'
                                            ];
                                            echo $roleLabels[$userData['role']] ?? $userData['role'];
                                            ?>
                                        </td>
                                        <td><?= $userData['study_program'] ?? '-' ?></td>
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
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <span id="delete-user-name"
                            class="font-weight-bold"></span>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <form id="delete-form" action="" method="post">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set up delete modal
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');

            var modal = $(this);
            modal.find('#delete-user-name').text(name);
            modal.find('#delete-form').attr('action', '<?= base_url('user/delete/') ?>' + id);
        });
    </script>
<?php endif; ?>
<?= $this->endSection() ?>