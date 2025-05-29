<?php
$currentPage = $pagination['currentPage'];
$totalPages = $pagination['totalPages'];
$baseUrl = $pagination['baseUrl'];
$searchQuery = $pagination['searchQuery'] ?? '';
$perPageQuery = $pagination['perPageQuery'] ?? '';
$sortQuery = $pagination['sortQuery'] ?? '';
$hasPrevious = $pagination['hasPrevious'] ?? false;
$hasNext = $pagination['hasNext'] ?? false;

$controls = [
    'first' => [
        'condition' => $currentPage > 1,
        'page' => 1,
        'title' => 'Halaman Pertama',
        'icon' => 'fas fa-angle-double-left'
    ],
    'previous' => [
        'condition' => $hasPrevious,
        'page' => $currentPage - 1,
        'title' => 'Halaman Sebelumnya',
        'icon' => 'fas fa-angle-left'
    ],
    'next' => [
        'condition' => $hasNext,
        'page' => $currentPage + 1,
        'title' => 'Halaman Selanjutnya',
        'icon' => 'fas fa-angle-right'
    ],
    'last' => [
        'condition' => $currentPage < $totalPages,
        'page' => $totalPages,
        'title' => 'Halaman Terakhir',
        'icon' => 'fas fa-angle-double-right'
    ]
];

$control = $controls[$type] ?? null;
if (!$control) return;
?>

<?php if ($control['condition']): ?>
    <li class="page-item">
        <a href="<?= $baseUrl ?>?page=<?= $control['page'] ?><?= $searchQuery ?><?= $perPageQuery ?><?= $sortQuery ?>"
            class="page-link" title="<?= $control['title'] ?>" data-toggle="tooltip">
            <i class="<?= $control['icon'] ?>"></i>
        </a>
    </li>
<?php else: ?>
    <li class="page-item disabled">
        <span class="page-link">
            <i class="<?= $control['icon'] ?>"></i>
        </span>
    </li>
<?php endif; ?>