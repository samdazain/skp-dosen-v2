<?php

/**
 * Integrity Status Component
 * 
 * @var int $score Integrity score
 */

$score = (int)($score ?? 0);

// Determine status based on score
if ($score >= 88) {
    $status = 'Sangat Baik';
    $class = 'text-success';
    $icon = 'fas fa-check-circle';
} elseif ($score >= 76) {
    $status = 'Baik';
    $class = 'text-primary';
    $icon = 'fas fa-thumbs-up';
} elseif ($score >= 61) {
    $status = 'Cukup';
    $class = 'text-warning';
    $icon = 'fas fa-exclamation-triangle';
} else {
    $status = 'Kurang';
    $class = 'text-danger';
    $icon = 'fas fa-times-circle';
}
?>

<span class="<?= $class ?> font-weight-bold">
    <i class="<?= $icon ?> mr-1"></i>
    <?= $status ?>
</span>