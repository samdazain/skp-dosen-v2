<?php
?>
<div class="col-md-4">
    <div class="card <?= $card_class ?? 'card-primary' ?>">
        <div class="card-header">
            <h3 class="card-title">
                <i class="<?= $icon ?? 'fas fa-cog' ?> mr-2"></i><?= $title ?? 'Action' ?>
            </h3>
        </div>
        <div class="card-body">
            <p><?= $description ?? 'Description here' ?></p>
            <div class="text-center pt-3">
                <i class="<?= $icon ?? 'fas fa-cog' ?> fa-5x <?= $icon_color ?? 'text-primary' ?> mb-3"></i>
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= base_url($action_url ?? '#') ?>" class="btn <?= $button_class ?? 'btn-primary' ?> btn-block">
                <i class="fas fa-external-link-alt mr-2"></i> <?= $button_text ?? 'Open' ?>
            </a>
        </div>
    </div>
</div>