<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/semester.css') ?>">

<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Kelola Semester',
    'breadcrumbs' => [
        ['text' => 'Home', 'url' => 'dashboard'],
        ['text' => 'Kelola Semester', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Messages -->
        <?php if (session()->get('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5><i class="icon fas fa-check-circle"></i> Berhasil!</h5>
                <?= esc(session()->get('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                <?= esc(session()->get('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->get('errors')): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Action Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Daftar Semester</h4>
                        <p class="text-muted mb-0">Kelola semester akademik untuk sistem SKP</p>
                    </div>
                    <a href="<?= base_url('semester/create') ?>" class="btn create-semester-btn">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Semester
                    </a>
                </div>
            </div>
        </div>

        <!-- Semester Grid -->
        <div class="row">
            <?php if (empty($semesters)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada data semester</h5>
                            <p class="text-muted">Mulai dengan menambahkan semester baru</p>
                            <a href="<?= base_url('semester/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Semester Pertama
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php
                $activeSemesterId = session()->get('activeSemesterId');
                foreach ($semesters as $semester):
                    $isActive = ($activeSemesterId == $semester['id']);
                    $termText = ($semester['term'] == '1') ? 'Ganjil' : 'Genap';
                    $yearRange = $semester['year'] . '/' . ($semester['year'] + 1);
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card semester-card <?= $isActive ? 'active-semester' : '' ?>">
                            <div class="card-header bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <?= esc($yearRange) ?>
                                    </h6>
                                    <?php if ($isActive): ?>
                                        <span class="badge badge-success semester-badge">
                                            <i class="fas fa-check mr-1"></i>
                                            Aktif
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h4 class="text-primary mb-2">Semester <?= esc($termText) ?></h4>
                                    <p class="text-muted mb-0">Tahun Akademik <?= esc($yearRange) ?></p>
                                </div>

                                <div class="semester-actions">
                                    <div class="btn-group w-100" role="group">
                                        <?php /* if (!$isActive): ?>
                                <button type="button" class="btn btn-outline-success btn-sm set-active-btn"
                                    data-semester-id="<?= $semester['id'] ?>" title="Set sebagai semester aktif">
                                    <i class="fas fa-check"></i>
                                    Aktifkan
                                </button>
                                <?php endif; */ ?>

                                        <a href="<?= base_url('semester/edit/' . $semester['id']) ?>"
                                            class="btn btn-outline-primary btn-sm" title="Edit semester">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>

                                        <?php if (!$isActive): ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm delete-semester-btn"
                                                data-semester-id="<?= $semester['id'] ?>"
                                                data-semester-name="Semester <?= esc($termText) ?> <?= esc($yearRange) ?>"
                                                title="Hapus semester">
                                                <i class="fas fa-trash"></i>
                                                Hapus
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($semester['created_at'])): ?>
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">
                                        <i class="fas fa-clock mr-1"></i>
                                        Dibuat: <?= date('d M Y', strtotime($semester['created_at'])) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>>

    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSemesterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus <strong id="semesterNameToDelete"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
                <form id="deleteSemesterForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Semester
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Set Active Semester
        $('.set-active-btn').on('click', function() {
            const semesterId = $(this).data('semester-id');

            $.ajax({
                url: '<?= base_url('semester/change') ?>',
                method: 'POST',
                data: {
                    semester_id: semesterId,
                    redirect: '<?= current_url() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        alert('Gagal mengaktifkan semester: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengaktifkan semester');
                }
            });
        });

        // Delete Semester
        $('.delete-semester-btn').on('click', function() {
            const semesterId = $(this).data('semester-id');
            const semesterName = $(this).data('semester-name');

            $('#semesterNameToDelete').text(semesterName);
            $('#deleteSemesterForm').attr('action', '<?= base_url('semester/delete/') ?>' + semesterId);
            $('#deleteSemesterModal').modal('show');
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?= $this->endSection() ?>