<?php if (!empty($program)): ?>
    <span class="badge badge-info px-2 py-1 d-inline-block text-truncate" style="max-width: 100%;">
        <i class="fas fa-graduation-cap mr-1"></i>
        <span class="badge-text"><?= esc($program) ?></span>
    </span>
<?php else: ?>
    <span class="text-muted">-</span>
<?php endif; ?>