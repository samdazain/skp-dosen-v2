<?php

/**
 * Score Badge Component
 * 
 * @var int $score Score value (0-100)
 * @var string $size Badge size ('sm', 'md', 'lg')
 */

$score = (int)($score ?? 0);
$size = $size ?? 'md';

// Determine badge class based on score
if ($score >= 85) {
    $badgeClass = 'badge-success';
    $icon = 'fas fa-trophy';
} elseif ($score >= 75) {
    $badgeClass = 'badge-primary';
    $icon = 'fas fa-star';
} elseif ($score >= 60) {
    $badgeClass = 'badge-warning';
    $icon = 'fas fa-exclamation-triangle';
} else {
    $badgeClass = 'badge-danger';
    $icon = 'fas fa-times-circle';
}

$sizeClass = $size === 'lg' ? 'badge-lg p-2' : ($size === 'sm' ? 'badge-sm' : '');
?>

<span class="badge <?= $badgeClass ?> <?= $sizeClass ?>" title="Skor: <?= $score ?> poin">
    <i class="<?= $icon ?> mr-1"></i>
    <?= $score ?>
</span>