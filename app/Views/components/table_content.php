<?php

/**
 * Reusable Table Content Component
 * 
 * @param array $data Table data
 * @param array $columns Column definitions
 * @param string $emptyMessage Message when no data
 * @param string $searchTerm Current search term for empty state
 */

$data = $data ?? [];
$columns = $columns ?? [];
$emptyMessage = $emptyMessage ?? 'Tidak ada data';
$searchTerm = $searchTerm ?? '';
?>

<div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped table-fixed">
        <?= view('Components/table_head', [
            'columns' => $columns,
            'sortBy' => $sortBy ?? $_GET['sort_by'] ?? 'name',
            'sortOrder' => $sortOrder ?? $_GET['sort_order'] ?? 'asc',
            'searchTerm' => $searchTerm,
            'perPage' => $perPage ?? $_GET['per_page'] ?? 10
        ]) ?>

        <tbody>
            <?php if (empty($data)): ?>
                <?= view('Components/empty_state', [
                    'message' => $emptyMessage,
                    'searchTerm' => $searchTerm,
                    'columns' => $columns
                ]) ?>
            <?php else: ?>
                <?= view('Components/table_rows', [
                    'data' => $data,
                    'columns' => $columns,
                    'pagination' => $pagination ?? []
                ]) ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>