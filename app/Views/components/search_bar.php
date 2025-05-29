<?php

/**
 * Reusable Search Bar Component
 * 
 * @param string $searchUrl Search form action URL
 * @param string $searchTerm Current search term
 * @param string $placeholder Search input placeholder
 * @param array $hiddenFields Hidden form fields to maintain state
 * @param bool $showResults Show search results info
 */

$searchUrl = $searchUrl ?? '';
$searchTerm = $searchTerm ?? '';
$placeholder = $placeholder ?? 'Cari data...';
$hiddenFields = $hiddenFields ?? [];
$showResults = $showResults ?? true;
?>

<div class="card-body pb-0">
    <form action="<?= $searchUrl ?>" method="get" class="row">
        <div class="col-md-4">
            <div class="input-group">
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="<?= esc($placeholder) ?>"
                    value="<?= esc($searchTerm) ?>">

                <?php foreach ($hiddenFields as $name => $value): ?>
                    <input type="hidden" name="<?= esc($name) ?>" value="<?= esc($value) ?>">
                <?php endforeach; ?>

                <div class="input-group-append">
                    <button type="submit" class="btn btn-default" title="Cari" data-toggle="tooltip">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($searchTerm)): ?>
                        <a href="<?= $searchUrl ?>"
                            class="btn btn-secondary"
                            title="Hapus Pencarian"
                            data-toggle="tooltip">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($showResults && !empty($searchTerm)): ?>
            <div class="col-md-8">
                <div class="text-muted mt-2">
                    <i class="fas fa-search mr-1"></i>
                    Menampilkan hasil pencarian untuk: <strong>"<?= esc($searchTerm) ?>"</strong>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>