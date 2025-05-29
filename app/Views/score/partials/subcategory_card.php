<?php

/**
 * Subcategory Card Partial
 * 
 * @var string $category
 * @var string $subKey
 * @var array $subcategory
 */
?>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0">
                <i class="fas fa-cogs text-primary mr-2"></i>
                <?= esc($subcategory['title']) ?>
            </h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-toggle="collapse"
                    data-target="#<?= $category ?>-<?= $subKey ?>-collapse" aria-expanded="true"
                    aria-controls="<?= $category ?>-<?= $subKey ?>-collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="<?= $category ?>-<?= $subKey ?>-collapse" class="collapse show">
        <div class="card-body">
            <form action="<?= base_url('score/update-ranges') ?>" method="post" class="score-range-form"
                data-category="<?= $category ?>" data-subcategory="<?= $subKey ?>">

                <?= csrf_field() ?>
                <input type="hidden" name="category" value="<?= $category ?>">
                <input type="hidden" name="subcategory" value="<?= $subKey ?>">

                <?= view('score/partials/range_table', [
                    'ranges' => $subcategory['ranges'],
                    'category' => $category,
                    'subcategory' => $subKey,
                    'subcategoryTitle' => $subcategory['title']
                ]) ?>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Total <?= count($subcategory['ranges']) ?> rentang nilai
                        <?php if (count($subcategory['ranges']) > 0): ?>
                            <span class="ml-2">
                                <i class="fas fa-save mr-1"></i>
                                Tekan "Simpan Perubahan" untuk menyimpan
                            </span>
                        <?php endif; ?>
                    </small>
                    <button type="submit" class="btn btn-primary"
                        <?= count($subcategory['ranges']) === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-save mr-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>