<?php
$isLeadership = in_array($position, ['DEKAN', 'WAKIL DEKAN I', 'WAKIL DEKAN II', 'WAKIL DEKAN III']);
$badgeClass = $isLeadership ? 'badge-primary' : 'badge-secondary';
$icon = $isLeadership ? 'fas fa-crown' : 'fas fa-user';
?>
<span class="badge <?= $badgeClass ?> px-2 py-1 d-inline-block text-truncate" style="max-width: 100%;">
    <i class="<?= $icon ?> mr-1"></i>
    <span class="badge-text"><?= esc($position) ?></span>
</span>