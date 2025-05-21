<?php
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $header_title ?? 'Page Title' ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php foreach ($breadcrumbs ?? [] as $item): ?>
                        <?php if (isset($item['active']) && $item['active']): ?>
                            <li class="breadcrumb-item active"><?= $item['text'] ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url($item['url'] ?? '#') ?>"><?= $item['text'] ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</div>