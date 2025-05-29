<?php

/**
 * Range Input Partial
 * Handles different types of range inputs (numeric, boolean, fixed)
 * 
 * @var array $range
 * @var string $fieldType
 */
?>

<?php if (isset($range['type']) && $range['type'] == 'boolean'): ?>
    <!-- Boolean type ranges -->
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-<?= $range['value'] ? 'check' : 'times' ?> text-<?= $range['value'] ? 'success' : 'danger' ?>"></i>
            </span>
        </div>
        <input type="text"
            class="form-control"
            name="ranges[<?= $range['id'] ?>][label]"
            value="<?= esc($range['label']) ?>"
            required>
        <input type="hidden" name="ranges[<?= $range['id'] ?>][type]" value="boolean">
        <input type="hidden" name="ranges[<?= $range['id'] ?>][value]" value="<?= $range['value'] ? 'true' : 'false' ?>">
    </div>

<?php elseif (isset($range['type']) && $range['type'] == 'fixed'): ?>
    <!-- Fixed type ranges -->
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-tag"></i>
            </span>
        </div>
        <input type="text"
            class="form-control"
            name="ranges[<?= $range['id'] ?>][label]"
            value="<?= esc($range['label']) ?>"
            required>
        <input type="hidden" name="ranges[<?= $range['id'] ?>][type]" value="fixed">
    </div>

<?php else: ?>
    <!-- Numeric ranges -->
    <div class="input-group">
        <?php if ($range['start'] === null): ?>
            <!-- Less than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-info">
                    <i class="fas fa-less-than"></i>
                </span>
            </div>
            <input type="number"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][end]"
                value="<?= $range['end'] ?>"
                step="0.01"
                required>
            <input type="hidden" name="ranges[<?= $range['id'] ?>][start]" value="">

        <?php elseif ($range['end'] === null): ?>
            <!-- Greater than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-success">
                    <i class="fas fa-greater-than"></i>
                </span>
            </div>
            <input type="number"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][start]"
                value="<?= $range['start'] ?>"
                step="0.01"
                required>
            <input type="hidden" name="ranges[<?= $range['id'] ?>][end]" value="">

        <?php else: ?>
            <!-- Between range -->
            <input type="number"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][start]"
                value="<?= $range['start'] ?>"
                step="0.01"
                required>
            <div class="input-group-prepend input-group-append">
                <span class="input-group-text">-</span>
            </div>
            <input type="number"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][end]"
                value="<?= $range['end'] ?>"
                step="0.01"
                required>
        <?php endif; ?>
    </div>
<?php endif; ?>