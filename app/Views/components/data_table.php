<?php

/**
 * Reusable Data Table Component
 * 
 * @param string $title Table title
 * @param string $icon Table icon class
 * @param array $data Table data
 * @param array $columns Column definitions
 * @param array $searchConfig Search configuration
 * @param array $exportConfig Export configuration
 * @param array $pagination Pagination data
 * @param string $addUrl URL for add new record button
 * @param string $addLabel Label for add button
 * @param string $emptyMessage Message when no data
 * @param string $cssFile Optional CSS file path
 * @param string $jsFile Optional JS file path
 */

// Set defaults
$title = $title ?? 'Data Table';
$icon = $icon ?? 'fas fa-table';
$data = $data ?? [];
$columns = $columns ?? [];
$searchConfig = $searchConfig ?? [];
$exportConfig = $exportConfig ?? [];
$pagination = $pagination ?? [];
$addUrl = $addUrl ?? '';
$addLabel = $addLabel ?? 'Tambah Data';
$emptyMessage = $emptyMessage ?? 'Tidak ada data';
$cssFile = $cssFile ?? '';
$jsFile = $jsFile ?? '';

// Extract sorting parameters
$sortBy = $sortBy ?? $_GET['sort_by'] ?? 'name';
$sortOrder = $sortOrder ?? $_GET['sort_order'] ?? 'asc';
$perPage = $perPage ?? $_GET['per_page'] ?? 10;
?>

<?php if ($cssFile): ?>
    <link rel="stylesheet" href="<?= base_url($cssFile) ?>">
<?php endif; ?>

<div class="card data-table-component">
    <?= view('Components/table_header', [
        'title' => $title,
        'icon' => $icon,
        'exportConfig' => $exportConfig,
        'addUrl' => $addUrl,
        'addLabel' => $addLabel
    ]) ?>

    <?php if (!empty($searchConfig)): ?>
        <?= view('Components/search_bar', $searchConfig) ?>
    <?php endif; ?>

    <?= view('Components/table_content', [
        'data' => $data,
        'columns' => $columns,
        'emptyMessage' => $emptyMessage,
        'searchTerm' => $searchConfig['searchTerm'] ?? '',
        'sortBy' => $sortBy,
        'sortOrder' => $sortOrder,
        'perPage' => $perPage,
        'pagination' => $pagination
    ]) ?>

    <?php if (!empty($data) && !empty($pagination) && $pagination['hasPages']): ?>
        <div class="card-footer">
            <?= view('Components/pagination', [
                'pagination' => $pagination,
                'size' => 'sm',
                'alignment' => 'end',
                'showInfo' => true,
                'showPerPage' => true,
                'perPageOptions' => [10, 25, 50, 100]
            ]) ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($jsFile): ?>
    <script src="<?= base_url($jsFile) ?>"></script>
<?php endif; ?>