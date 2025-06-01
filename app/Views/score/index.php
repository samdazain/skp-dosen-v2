<?php

/**
 * Score Management Index View
 * 
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<?= view('score/partials/header') ?>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Messages -->
        <?= view('score/partials/alerts') ?>

        <!-- Info Box -->
        <?= view('score/partials/info_box') ?>

        <!-- Score Range Configuration -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs mr-2"></i>
                            Konfigurasi Rentang Nilai SKP
                        </h3>
                        <div class="card-tools">
                            <a href="<?= base_url('score/export-config') ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-download mr-1"></i> Export Konfigurasi
                            </a>
                            <button type="button" class="btn btn-sm btn-warning" onclick="confirmReset()">
                                <i class="fas fa-undo mr-1"></i> Reset ke Default
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <?php if (empty($scoreRanges)): ?>
                            <!-- Empty state -->
                            <div class="text-center py-5">
                                <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Belum Ada Konfigurasi Nilai</h4>
                                <p class="text-muted">Mulai dengan menginisialisasi pengaturan nilai default.</p>
                                <a href="<?= base_url('score/reset-to-default') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Inisialisasi Pengaturan Default
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Tab Navigation -->
                            <?= view('score/partials/tab_navigation', ['scoreRanges' => $scoreRanges]) ?>

                            <!-- Tab Content -->
                            <?= view('score/partials/tab_content', ['scoreRanges' => $scoreRanges]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modals -->
<?= view('score/partials/modals') ?>

<script>
    function confirmReset() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Reset Pengaturan Nilai?',
                text: 'Ini akan menghapus semua kustomisasi dan mengembalikan ke pengaturan default.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('score/reset-to-default') ?>';
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin mereset semua pengaturan ke default?')) {
                window.location.href = '<?= base_url('score/reset-to-default') ?>';
            }
        }
    }
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page-specific scripts -->
<?= view('score/partials/scripts') ?>
<?= $this->endSection() ?>