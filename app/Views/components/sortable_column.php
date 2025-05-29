<?php
// Extract column data from the passed array
$columnField = $column['field'] ?? '';
$columnLabel = $column['label'] ?? '';
$columnClass = $column['class'] ?? '';

// Get current sorting parameters from the request or passed data
$currentSortBy = $sortBy ?? $_GET['sort_by'] ?? 'name';
$currentSortOrder = $sortOrder ?? $_GET['sort_order'] ?? 'asc';
$currentSearch = $searchTerm ?? $_GET['search'] ?? '';
$currentPerPage = $perPage ?? $_GET['per_page'] ?? 10;
?>

<th class="<?= $columnClass ?> sortable-header">
    <div class="sort-container">
        <a href="<?= getSortUrl($columnField, $currentSortBy, $currentSortOrder, $currentSearch, $currentPerPage) ?>"
            class="sort-link">
            <span class="sort-text"><?= esc($columnLabel) ?></span>
            <span class="sort-icon-container">
                <?= getSortIcon($columnField, $currentSortBy, $currentSortOrder) ?>
            </span>
        </a>
    </div>
</th>