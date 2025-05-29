<?php

/**
 * Score Management Main View
 * 
 * @var CodeIgniter\View\View $this
 * @var array $scoreRanges
 * @var array $user
 * @var string $pageTitle
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<?= view('score/partials/header') ?>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Messages -->
        <?= view('score/partials/alerts') ?>

        <!-- Info Box -->
        <?= view('score/partials/info_box') ?>

        <!-- Score Range Configuration Tabs -->
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <?= view('score/partials/tab_navigation', ['scoreRanges' => $scoreRanges]) ?>
            </div>

            <div class="card-body">
                <div class="tab-content" id="score-tabContent">
                    <?php
                    $isFirst = true;
                    foreach ($scoreRanges as $key => $category):
                    ?>
                        <div class="tab-pane fade <?= $isFirst ? 'show active' : '' ?>" id="<?= $key ?>-content"
                            role="tabpanel" aria-labelledby="<?= $key ?>-tab">

                            <div class="mb-3">
                                <h4 class="text-primary">
                                    <i class="fas fa-cogs mr-2"></i>
                                    <?= esc($category['title']) ?>
                                </h4>
                                <hr class="border-primary">
                            </div>

                            <?php foreach ($category['subcategories'] as $subKey => $subcategory): ?>
                                <?= view('score/partials/subcategory_card', [
                                    'category' => $key,
                                    'subKey' => $subKey,
                                    'subcategory' => $subcategory
                                ]) ?>
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

<!-- Modals -->
<?= view('score/partials/modals') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page-specific scripts -->
<?= view('score/partials/scripts') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/score_management.css') ?>">
<?= $this->endSection() ?>