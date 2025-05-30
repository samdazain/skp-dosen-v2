<?php

/**
 * Table Header Component
 */

$tableColumns = [
    ['class' => 'text-center', 'style' => 'width: 50px;', 'sortable' => false, 'title' => 'No'],
    ['class' => 'sortable', 'data-sort' => 'name', 'sortable' => true, 'title' => 'Nama Dosen'],
    ['class' => 'sortable', 'data-sort' => 'study_program', 'sortable' => true, 'title' => 'Program Studi'],
    ['class' => 'text-center', 'sortable' => false, 'title' => 'Absen Harian<br>(Jumlah Alpha)'],
    ['class' => 'text-center', 'sortable' => false, 'title' => 'Absen Senam Pagi<br>(Jumlah Alpha)'],
    ['class' => 'text-center', 'sortable' => false, 'title' => 'Absen Upacara<br>(Jumlah Alpha)'],
    ['class' => 'text-center', 'sortable' => false, 'title' => 'Nilai Akhir'],
    ['class' => 'text-center', 'sortable' => false, 'title' => 'Status'],
];
?>

<thead class="thead-dark">
    <tr>
        <?php foreach ($tableColumns as $column): ?>
            <th class="<?= $column['class'] ?>"
                <?= isset($column['style']) ? 'style="' . $column['style'] . '"' : '' ?>
                <?= isset($column['data-sort']) ? 'data-sort="' . $column['data-sort'] . '"' : '' ?>>
                <?= $column['title'] ?>
                <?php if ($column['sortable']): ?>
                    <i class="fas fa-sort sort-icon ml-1"></i>
                <?php endif; ?>
            </th>
        <?php endforeach; ?>
    </tr>
</thead>