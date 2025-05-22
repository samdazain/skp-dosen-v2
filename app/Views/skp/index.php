<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Data Master SKP',
    'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => 'dashboard'],
        ['text' => 'Data Master SKP', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?= view('components/alerts') ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Penilaian Kinerja Dosen</h3>
                        <div class="card-tools d-flex align-items-center">
                            <a href="<?= base_url('skp/export-excel') ?>" class="btn btn-sm btn-success mr-1">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </a>
                            <a href="<?= base_url('skp/export-pdf') ?>" class="btn btn-sm btn-danger mr-2">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF
                            </a>
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" id="searchInput"
                                    placeholder="Cari...">
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
                                    <th class="text-center">Integritas</th>
                                    <th class="text-center">Disiplin</th>
                                    <th class="text-center">Komitmen</th>
                                    <th class="text-center">Kerjasama</th>
                                    <th class="text-center">Orientasi<br>Pelayanan</th>
                                    <th class="text-center">Nilai Total</th>
                                    <th class="text-center">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lecturers as $i => $lecturer): ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= $lecturer['name'] ?></td>
                                        <td><?= $lecturer['nip'] ?></td>
                                        <td class="text-center"><?= $lecturer['study_program'] ?></td>
                                        <td class="text-center"><?= $lecturer['integrity'] ?></td>
                                        <td class="text-center"><?= $lecturer['discipline'] ?></td>
                                        <td class="text-center"><?= $lecturer['commitment'] ?></td>
                                        <td class="text-center"><?= $lecturer['cooperation'] ?></td>
                                        <td class="text-center"><?= $lecturer['service'] ?></td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-<?= $lecturer['badge_color'] ?> px-3 py-2"><?= $lecturer['total'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-<?= $lecturer['badge_color'] ?>"><?= $lecturer['category'] ?></span>
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

                <!-- Summary Cards -->
                <div class="row mt-4">
                    <?= view('skp/partials/summary_card', [
                        'title' => 'Sangat Baik',
                        'icon' => 'fas fa-thumbs-up',
                        'color' => 'success',
                        'count' => $stats['sangat_baik']['count'],
                        'percentage' => $stats['sangat_baik']['percentage']
                    ]) ?>

                    <?= view('skp/partials/summary_card', [
                        'title' => 'Baik',
                        'icon' => 'fas fa-check',
                        'color' => 'primary',
                        'count' => $stats['baik']['count'],
                        'percentage' => $stats['baik']['percentage']
                    ]) ?>

                    <?= view('skp/partials/summary_card', [
                        'title' => 'Cukup',
                        'icon' => 'fas fa-exclamation',
                        'color' => 'warning',
                        'count' => $stats['cukup']['count'],
                        'percentage' => $stats['cukup']['percentage']
                    ]) ?>

                    <?= view('skp/partials/summary_card', [
                        'title' => 'Kurang',
                        'icon' => 'fas fa-times',
                        'color' => 'danger',
                        'count' => $stats['kurang']['count'],
                        'percentage' => $stats['kurang']['percentage']
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/skp/index.js') ?>"></script>
<?= $this->endSection() ?>