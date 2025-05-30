<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                    Data Integritas Dosen
                </h1>
                <p class="text-muted mt-1">
                    Monitoring tingkat kehadiran dan beban mengajar dosen
                </p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('dashboard') ?>">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Data Integritas</li>
                </ol>
            </div>
        </div>

        <!-- Semester Info -->
        <div class="row">
            <div class="col-12">
                <?= view('components/semester_info') ?>
            </div>
        </div>
    </div>
</div>