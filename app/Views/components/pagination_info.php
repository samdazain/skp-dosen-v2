<div class="d-flex align-items-center flex-wrap">
    <?php if ($showInfo): ?>
        <div class="pagination-info mr-3 mb-2 mb-md-0">
            <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan
                <strong><?= number_format($pagination['startRecord']) ?></strong> -
                <strong><?= number_format($pagination['endRecord']) ?></strong>
                dari
                <strong><?= number_format($pagination['total']) ?></strong>
                data
            </small>
        </div>
    <?php endif; ?>

    <?php if ($showPerPage): ?>
        <div class="per-page-selector">
            <select class="form-control form-control-sm pagination-per-page-select"
                style="width: 80px; display: inline-block;"
                data-current-per-page="<?= $pagination['perPage'] ?>">
                <?php foreach ($perPageOptions as $option): ?>
                    <option value="<?= $option ?>" <?= ($pagination['perPage'] == $option) ? 'selected' : '' ?>>
                        <?= $option ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted ml-1">per halaman</small>
        </div>
    <?php endif; ?>
</div>