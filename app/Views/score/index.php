<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pengaturan Rentang Nilai</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan Nilai</li>
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

        <!-- Info Box -->
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> Petunjuk Pengaturan Nilai</h5>
            <p>Halaman ini memungkinkan Anda untuk mengatur rentang nilai yang digunakan dalam sistem penilaian SKP
                Dosen.
                Perubahan pada pengaturan ini akan mempengaruhi cara nilai dihitung pada seluruh fitur aplikasi.</p>
        </div>

        <!-- Score Range Configuration Tabs -->
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="score-tab" role="tablist">
                    <?php
                    $isFirst = true;
                    foreach ($scoreRanges as $key => $category):
                    ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $isFirst ? 'active' : '' ?>" id="<?= $key ?>-tab" data-toggle="pill"
                                href="#<?= $key ?>-content" role="tab" aria-controls="<?= $key ?>-content"
                                aria-selected="<?= $isFirst ? 'true' : 'false' ?>">
                                <?= $category['title'] ?>
                            </a>
                        </li>
                    <?php
                        $isFirst = false;
                    endforeach;
                    ?>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="score-tabContent">
                    <?php
                    $isFirst = true;
                    foreach ($scoreRanges as $key => $category):
                    ?>
                        <div class="tab-pane fade <?= $isFirst ? 'show active' : '' ?>" id="<?= $key ?>-content"
                            role="tabpanel" aria-labelledby="<?= $key ?>-tab">

                            <h4 class="mb-3"><?= $category['title'] ?></h4>

                            <?php foreach ($category['subcategories'] as $subKey => $subcategory): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title m-0"><?= $subcategory['title'] ?></h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-toggle="collapse"
                                                data-target="#<?= $key ?>-<?= $subKey ?>-collapse" aria-expanded="true"
                                                aria-controls="<?= $key ?>-<?= $subKey ?>-collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="<?= $key ?>-<?= $subKey ?>-collapse" class="collapse show">
                                        <div class="card-body">
                                            <form action="<?= base_url('score/update-ranges') ?>" method="post"
                                                class="score-range-form">
                                                <input type="hidden" name="category" value="<?= $key ?>">
                                                <input type="hidden" name="subcategory" value="<?= $subKey ?>">

                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 40%">Rentang</th>
                                                                <th style="width: 25%">Nilai</th>
                                                                <th style="width: 35%">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($subcategory['ranges'] as $index => $range): ?>
                                                                <tr data-range-id="<?= $range['id'] ?>">
                                                                    <?php if (isset($range['type']) && $range['type'] == 'boolean'): ?>
                                                                        <!-- Boolean type ranges (like Kompetensi - aktif/tidak) -->
                                                                        <td>
                                                                            <input type="text" class="form-control"
                                                                                name="ranges[<?= $range['id'] ?>][label]"
                                                                                value="<?= $range['label'] ?>" required>
                                                                            <input type="hidden"
                                                                                name="ranges[<?= $range['id'] ?>][type]"
                                                                                value="boolean">
                                                                            <input type="hidden"
                                                                                name="ranges[<?= $range['id'] ?>][value]"
                                                                                value="<?= $range['value'] ? 'true' : 'false' ?>">
                                                                        </td>
                                                                    <?php elseif (isset($range['type']) && $range['type'] == 'fixed'): ?>
                                                                        <!-- Fixed type ranges (like Kerjasama - levels) -->
                                                                        <td>
                                                                            <input type="text" class="form-control"
                                                                                name="ranges[<?= $range['id'] ?>][label]"
                                                                                value="<?= $range['label'] ?>" required>
                                                                            <input type="hidden"
                                                                                name="ranges[<?= $range['id'] ?>][type]" value="fixed">
                                                                        </td>
                                                                    <?php else: ?>
                                                                        <!-- Numeric ranges -->
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <?php if ($range['start'] === null): ?>
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i
                                                                                                class="fas fa-less-than"></i></span>
                                                                                    </div>
                                                                                    <input type="number" class="form-control"
                                                                                        name="ranges[<?= $range['id'] ?>][end]"
                                                                                        value="<?= $range['end'] ?>" step="0.01" required>
                                                                                    <input type="hidden"
                                                                                        name="ranges[<?= $range['id'] ?>][start]" value="">
                                                                                <?php elseif ($range['end'] === null): ?>
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text"><i
                                                                                                class="fas fa-greater-than"></i></span>
                                                                                    </div>
                                                                                    <input type="number" class="form-control"
                                                                                        name="ranges[<?= $range['id'] ?>][start]"
                                                                                        value="<?= $range['start'] ?>" step="0.01" required>
                                                                                    <input type="hidden"
                                                                                        name="ranges[<?= $range['id'] ?>][end]" value="">
                                                                                <?php else: ?>
                                                                                    <input type="number" class="form-control"
                                                                                        name="ranges[<?= $range['id'] ?>][start]"
                                                                                        value="<?= $range['start'] ?>" step="0.01" required>
                                                                                    <div class="input-group-prepend input-group-append">
                                                                                        <span class="input-group-text">-</span>
                                                                                    </div>
                                                                                    <input type="number" class="form-control"
                                                                                        name="ranges[<?= $range['id'] ?>][end]"
                                                                                        value="<?= $range['end'] ?>" step="0.01" required>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </td>
                                                                    <?php endif; ?>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            name="ranges[<?= $range['id'] ?>][score]"
                                                                            value="<?= $range['score'] ?>" required min="0"
                                                                            max="100">
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm delete-range"
                                                                                data-toggle="modal" data-target="#deleteRangeModal"
                                                                                data-range-id="<?= $range['id'] ?>">
                                                                                <i class="fas fa-trash"></i> Hapus
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <button type="button" class="btn btn-sm btn-info add-range"
                                                                        data-toggle="modal" data-target="#addRangeModal"
                                                                        data-category="<?= $key ?>"
                                                                        data-subcategory="<?= $subKey ?>">
                                                                        <i class="fas fa-plus"></i> Tambah Rentang Nilai
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <div class="mt-3 text-right">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php
                        $isFirst = false;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Range Modal -->
<div class="modal fade" id="addRangeModal" tabindex="-1" role="dialog" aria-labelledby="addRangeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRangeModalLabel">Tambah Rentang Nilai Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('score/add-range') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="category" id="addRangeCategory">
                    <input type="hidden" name="subcategory" id="addRangeSubcategory">

                    <div class="form-group">
                        <label for="rangeType">Tipe Rentang</label>
                        <select class="form-control" id="rangeType" name="range_type" required>
                            <option value="range">Rentang Numerik</option>
                            <option value="above">Lebih Dari (>)</option>
                            <option value="below">Kurang Dari (<)< /option>
                            <option value="fixed">Nilai Tetap</option>
                            <option value="boolean">Boolean (Ya/Tidak)</option>
                        </select>
                    </div>

                    <div id="numericRangeFields">
                        <div class="form-group" id="rangeStartGroup">
                            <label for="rangeStart">Nilai Awal</label>
                            <input type="number" class="form-control" id="rangeStart" name="range_start" step="0.01">
                        </div>

                        <div class="form-group" id="rangeEndGroup">
                            <label for="rangeEnd">Nilai Akhir</label>
                            <input type="number" class="form-control" id="rangeEnd" name="range_end" step="0.01">
                        </div>
                    </div>

                    <div class="form-group" id="labelGroup">
                        <label for="rangeLabel">Label</label>
                        <input type="text" class="form-control" id="rangeLabel" name="range_label">
                        <small class="form-text text-muted">Misalnya: '1-2', 'Ada', 'Tidak Kooperatif', dll.</small>
                    </div>

                    <div class="form-group">
                        <label for="score">Nilai</label>
                        <input type="number" class="form-control" id="score" name="score" required min="0" max="100">
                        <small class="form-text text-muted">Nilai yang akan diberikan untuk rentang ini (0-100)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Range Modal -->
<div class="modal fade" id="deleteRangeModal" tabindex="-1" role="dialog" aria-labelledby="deleteRangeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRangeModalLabel">Hapus Rentang Nilai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('score/delete-range') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="range_id" id="deleteRangeId">
                    <p>Apakah Anda yakin ingin menghapus rentang nilai ini? Penghapusan ini tidak dapat dibatalkan.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Perhatian: Menghapus rentang nilai dapat
                        mempengaruhi perhitungan skor pada data yang ada.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up the range type selector in the add range modal
        const rangeType = document.getElementById('rangeType');
        const rangeStartGroup = document.getElementById('rangeStartGroup');
        const rangeEndGroup = document.getElementById('rangeEndGroup');
        const labelGroup = document.getElementById('labelGroup');

        function updateRangeFields() {
            const type = rangeType.value;

            switch (type) {
                case 'range':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'block';
                    labelGroup.style.display = 'none';
                    break;
                case 'above':
                    rangeStartGroup.style.display = 'block';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'none';
                    break;
                case 'below':
                    rangeStartGroup.style.display = 'none';
                    rangeEndGroup.style.display = 'block';
                    labelGroup.style.display = 'none';
                    break;
                case 'fixed':
                case 'boolean':
                    rangeStartGroup.style.display = 'none';
                    rangeEndGroup.style.display = 'none';
                    labelGroup.style.display = 'block';
                    break;
            }
        }

        rangeType.addEventListener('change', updateRangeFields);
        updateRangeFields(); // Initial setup

        // Set up add range modal data
        const addRangeButtons = document.querySelectorAll('.add-range');
        addRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.dataset.category;
                const subcategory = this.dataset.subcategory;

                document.getElementById('addRangeCategory').value = category;
                document.getElementById('addRangeSubcategory').value = subcategory;
            });
        });

        // Set up delete range modal data
        const deleteRangeButtons = document.querySelectorAll('.delete-range');
        deleteRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const rangeId = this.dataset.rangeId;
                document.getElementById('deleteRangeId').value = rangeId;
            });
        });
    });
</script>

<?= $this->endSection() ?>