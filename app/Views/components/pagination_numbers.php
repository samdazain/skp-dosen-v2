<?php
$currentPage = $pagination['currentPage'];
$totalPages = $pagination['totalPages'];
$baseUrl = $pagination['baseUrl'];
$searchQuery = $pagination['searchQuery'] ?? '';
$perPageQuery = $pagination['perPageQuery'] ?? '';
$sortQuery = $pagination['sortQuery'] ?? '';

// Calculate pagination range
$start = max(1, $currentPage - 2);
$end = min($totalPages, $currentPage + 2);
?>

<?php if ($start > 1): ?>
    <li class="page-item">
        <a href="<?= $baseUrl ?>?page=1<?= $searchQuery ?><?= $perPageQuery ?><?= $sortQuery ?>"
            class="page-link">1</a>
    </li>
    <?php if ($start > 2): ?>
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php for ($i = $start; $i <= $end; $i++): ?>
    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
        <?php if ($i == $currentPage): ?>
            <span class="page-link font-weight-bold"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= $baseUrl ?>?page=<?= $i ?><?= $searchQuery ?><?= $perPageQuery ?><?= $sortQuery ?>"
                class="page-link"><?= $i ?></a>
        <?php endif; ?>
    </li>
<?php endfor; ?>

<?php if ($end < $totalPages): ?>
    <?php if ($end < $totalPages - 1): ?>
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>
    <?php endif; ?>
    <li class="page-item">
        <a href="<?= $baseUrl ?>?page=<?= $totalPages ?><?= $searchQuery ?><?= $perPageQuery ?><?= $sortQuery ?>"
            class="page-link"><?= $totalPages ?></a>
    </li>
<?php endif; ?>