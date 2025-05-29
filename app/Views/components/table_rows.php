<?php
$startNumber = isset($pagination) ? $pagination['startRecord'] : 1;
?>

<?php foreach ($data as $index => $row): ?>
    <tr>
        <?php foreach ($columns as $column): ?>
            <td class="<?= $column['class'] ?? '' ?>">
                <?php if (isset($column['render'])): ?>
                    <?= $column['render']($row, $startNumber + $index) ?>
                <?php else: ?>
                    <?= esc($row[$column['field']] ?? '') ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>