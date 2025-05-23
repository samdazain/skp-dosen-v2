<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

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
                <?php foreach ($lecturers as $i => $lecturer): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= $lecturer['name'] ?></td>
                        <td><?= $lecturer['nip'] ?></td>
                        <td class="text-center"><?= $lecturer['study_program'] ?></td>
                        <td class="text-center"><?= $lecturer['position'] ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?= base_url('lecturers/edit/' . ($i + 1)) ?>" class="btn btn-sm btn-warning"
                                    title="Edit">
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
        <?php if (isset($pager)): ?>
            <?= $pager->links() ?>
        <?php else: ?>
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div>