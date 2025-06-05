<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('styles') ?>
<style>
    .form-card {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 12px;
    }

    .form-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
    }

    .form-control {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .btn-submit {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }

    .btn-cancel {
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<?= view('components/content_header', [
    'header_title' => 'Tambah Semester',
    'breadcrumbs' => [
        ['text' => 'Home', 'url' => 'dashboard'],
        ['text' => 'Kelola Semester', 'url' => 'semester'],
        ['text' => 'Tambah Semester', 'active' => true]
    ]
]) ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Messages -->
        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                <?= esc(session()->get('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->get('errors')): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                <ul class="mb-0">
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card">
                    <div class="card-header form-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Tambah Semester Baru
                        </h5>
                    </div>

                    <?= form_open(base_url('semester/store'), ['class' => 'needs-validation', 'novalidate' => true]) ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="year">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Tahun Akademik
                            </label>
                            <input type="number" class="form-control" id="year" name="year"
                                value="<?= old('year', date('Y')) ?>" min="2020" max="2030" required>
                            <div class="invalid-feedback">
                                Tahun harus diisi dengan format 4 digit (contoh: 2024)
                            </div>
                            <small class="form-text text-muted">
                                Masukkan tahun dimulainya semester (contoh: 2024 untuk tahun akademik 2024/2025)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="term">
                                <i class="fas fa-list-alt mr-2"></i>
                                Semester
                            </label>
                            <select class="form-control" id="term" name="term" required>
                                <option value="">Pilih Semester</option>
                                <option value="1" <?= old('term') == '1' ? 'selected' : '' ?>>Ganjil</option>
                                <option value="2" <?= old('term') == '2' ? 'selected' : '' ?>>Genap</option>
                            </select>
                            <div class="invalid-feedback">
                                Pilih semester (Ganjil atau Genap)
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="form-group">
                            <label>Preview:</label>
                            <div class="alert alert-info" id="semesterPreview">
                                <i class="fas fa-eye mr-2"></i>
                                <span id="previewText">Pilih tahun dan semester untuk melihat preview</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('semester') ?>" class="btn btn-secondary btn-cancel">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success btn-submit">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Semester
                            </button>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Update preview when year or term changes
        function updatePreview() {
            const year = $('#year').val();
            const term = $('#term').val();

            if (year && term) {
                const termText = term === '1' ? 'Ganjil' : 'Genap';
                const yearRange = year + '/' + (parseInt(year) + 1);
                const previewText = `Semester ${termText} ${yearRange}`;

                $('#previewText').text(previewText);
                $('#semesterPreview').removeClass('alert-info').addClass('alert-success');
            } else {
                $('#previewText').text('Pilih tahun dan semester untuk melihat preview');
                $('#semesterPreview').removeClass('alert-success').addClass('alert-info');
            }
        }

        $('#year, #term').on('change input', updatePreview);

        // Form validation
        $('form').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').not('#semesterPreview').fadeOut('slow');
        }, 5000);

        // Set default focus
        $('#year').focus();

        // Initialize preview
        updatePreview();
    });
</script>
<?= $this->endSection() ?>