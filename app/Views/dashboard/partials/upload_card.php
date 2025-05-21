<?php
// Remove the var_dump
if (isset($params) && is_array($params)) {
    extract($params);
}
?>

<div class="col-md-4">
    <div class="card <?= $card_class ?? 'card-primary' ?>">
        <div class="card-header">
            <h3 class="card-title">
                <i class="<?= $icon ?? 'fas fa-upload' ?> mr-2"></i><?= $title ?? 'Upload Data' ?>
            </h3>
        </div>
        <div class="card-body">
            <div class="upload-indicator text-center mb-3">
                <div class="upload-icon-container position-relative">
                    <i class="fas fa-file-excel fa-3x text-success"></i>
                    <i
                        class="fas fa-arrow-circle-up position-absolute <?= $arrow_color ?? 'text-primary' ?> upload-arrow"></i>
                </div>
                <p class="mt-2 text-muted">Klik untuk memilih file Excel</p>
            </div>
            <form action="<?= base_url($upload_url ?? '#') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="<?= $input_id ?? 'fileInput' ?>"
                            name="<?= $input_name ?? 'file' ?>" accept=".xlsx,.xls,.csv">
                        <label class="custom-file-label" for="<?= $input_id ?? 'fileInput' ?>">
                            <i class="fas fa-upload mr-1"></i> Pilih file Excel
                        </label>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn <?= $button_class ?? 'btn-primary' ?> btn-block">
                        <i class="fas fa-upload mr-2"></i> Upload
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="<?= $download_url ?? '#' ?>" class="text-muted">
                <i class="fas fa-download mr-1"></i> Download Uploaded File
            </a>
        </div>
    </div>
</div>