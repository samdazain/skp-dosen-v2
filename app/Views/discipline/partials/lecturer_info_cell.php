<?php

/**
 * Lecturer Info Cell Component
 * 
 * @var array $lecturer
 */
?>

<div class="user-panel">
    <div class="info">
        <strong><?= esc($lecturer['lecturer_name']) ?></strong><br>
        <small class="text-muted"><?= esc($lecturer['nip']) ?></small><br>
        <small class="text-info"><?= esc($lecturer['position']) ?></small>
    </div>
</div>