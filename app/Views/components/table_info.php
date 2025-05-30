<?php

/**
 * Table Info Component
 * 
 * @var int $total Total number of records
 * @var string $type Type of records (e.g., 'dosen', 'mahasiswa')
 */

$total = (int)($total ?? 0);
$type = $type ?? 'record';
?>

<small class="text-muted">
    <i class="fas fa-info-circle mr-1"></i>
    Menampilkan <?= $total ?> <?= $type ?>
    <?php if ($total > 0): ?>
        <span class="ml-2">
            <i class="fas fa-clock mr-1"></i>
            Terakhir diperbarui: <?= date('d M Y H:i') ?>
        </span>
    <?php endif; ?>
</small>