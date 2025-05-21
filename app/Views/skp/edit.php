<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit SKP Dosen</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('skp') ?>">Data SKP</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                        <h3 class="card-title">Edit Penilaian Kinerja Dosen</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('skp') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Batal
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-center py-5">
                            Edit form placeholder for SKP ID: <?= $id ?>
                        </p>

                        <div class="text-center">
                            <a href="<?= base_url('skp') ?>" class="btn btn-secondary mr-2">Batal</a>
                            <a href="<?= base_url('skp') ?>" class="btn btn-primary">Simpan Perubahan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>