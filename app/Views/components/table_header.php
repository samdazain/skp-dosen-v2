<div class="card-header">
    <h3 class="card-title">
        <i class="<?= $icon ?> mr-2"></i>
        <?= esc($title) ?>
    </h3>
    <div class="card-tools">
        <?php if (!empty($exportConfig)): ?>
            <?= view('Components/export_buttons', $exportConfig) ?>
        <?php endif; ?>

        <?php if (!empty($addUrl)): ?>
            <a href="<?= $addUrl ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> <?= esc($addLabel) ?>
            </a>
        <?php endif; ?>
    </div>
</div>