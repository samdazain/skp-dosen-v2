<thead class="bg-light">
    <tr>
        <?php foreach ($columns as $column): ?>
            <?php if ($column['sortable'] ?? false): ?>
                <?= view('Components/sortable_column', [
                    'column' => $column,
                    'sortBy' => $sortBy ?? $_GET['sort_by'] ?? 'name',
                    'sortOrder' => $sortOrder ?? $_GET['sort_order'] ?? 'asc',
                    'searchTerm' => $searchTerm ?? $_GET['search'] ?? '',
                    'perPage' => $perPage ?? $_GET['per_page'] ?? 10
                ]) ?>
            <?php else: ?>
                <th class="<?= $column['class'] ?? '' ?>">
                    <?= esc($column['label'] ?? '') ?>
                </th>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
</thead>