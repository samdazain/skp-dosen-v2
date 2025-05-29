<?php

/**
 * Reusable Pagination Component
 * 
 * @param array $pagination Pagination data array
 * @param string $size Size of pagination (sm, md, lg) - default: sm
 * @param string $alignment Alignment (start, center, end) - default: end
 * @param bool $showInfo Show pagination info - default: true
 * @param bool $showPerPage Show per page selector - default: true
 * @param array $perPageOptions Available per page options - default: [10, 25, 50, 100]
 */

// Set default values
$size = $size ?? 'sm';
$alignment = $alignment ?? 'end';
$showInfo = $showInfo ?? true;
$showPerPage = $showPerPage ?? true;
$perPageOptions = $perPageOptions ?? [10, 25, 50, 100];

// Validate pagination data
if (empty($pagination) || !$pagination['hasPages']) {
    return;
}
?>

<div class="pagination-wrapper">
    <div class="row align-items-center">
        <?php if ($showInfo || $showPerPage): ?>
            <div class="col-md-6">
                <?= view('Components/pagination_info', [
                    'pagination' => $pagination,
                    'showInfo' => $showInfo,
                    'showPerPage' => $showPerPage,
                    'perPageOptions' => $perPageOptions
                ]) ?>
            </div>
        <?php endif; ?>

        <div class="<?= ($showInfo || $showPerPage) ? 'col-md-6' : 'col-12' ?>">
            <?= view('Components/pagination_nav', [
                'pagination' => $pagination,
                'size' => $size,
                'alignment' => $alignment
            ]) ?>
        </div>
    </div>
</div>