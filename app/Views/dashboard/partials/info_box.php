<?php
?>
<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box mb-3">
        <span class="info-box-icon <?= $bg_color ?? 'bg-primary' ?> elevation-1">
            <i class="<?= $icon ?? 'fas fa-info' ?>"></i>
        </span>
        <div class="info-box-content">
            <span class="info-box-text"><?= $title ?? 'Information' ?></span>
            <span class="info-box-number"><?= $value ?? '0' ?></span>
            <small class="text-muted"><?= $description ?? '' ?></small>
        </div>
    </div>
</div>