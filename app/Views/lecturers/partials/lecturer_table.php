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
            <form action="<?= base_url('lecturers') ?>" method="get"
                class="input-group input-group-sm ml-3 pt-1 float-right" style="width: 250px;">
                <input type="text" name="search" class="form-control float-right" id="searchInput"
                    placeholder="Cari dosen..." value="<?= $search ?? '' ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Dosen</th>
                    <th>NIP</th>
                    <th>Jabatan</th> <!-- Changed from Email to Jabatan -->
                    <th class="text-center">Program Studi</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lecturers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data dosen</td>
                    </tr>
                <?php else: ?>
                    <?php
                    $start = 0;
                    if (isset($pager)) {
                        $start = ($pager->getCurrentPage() - 1) * $pager->getPerPage();
                    }
                    ?>
                    <?php foreach ($lecturers as $i => $lecturer): ?>
                        <tr>
                            <td class="text-center"><?= $start + $i + 1 ?></td>
                            <td><?= esc($lecturer['name']) ?></td>
                            <td><?= esc($lecturer['nip']) ?></td>
                            <td><?= esc($lecturer['position']) ?></td> <!-- Changed from email to position -->
                            <td class="text-center">
                                <?= !empty($lecturer['study_program']) ? esc($lecturer['study_program']) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?= base_url('lecturers/edit/' . $lecturer['id']) ?>"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                        onclick="confirmDelete('<?= $lecturer['id'] ?>', '<?= esc($lecturer['name']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <?php if (isset($pager)): ?>
            <div class="float-right">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>