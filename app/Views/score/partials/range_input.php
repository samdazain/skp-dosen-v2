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
    <?php
    // Determine boolean value from label or existing value
    $boolValue = false;
    if (isset($range['value'])) {
        $boolValue = $range['value'];
    } elseif (isset($range['label'])) {
        $boolValue = in_array(strtolower($range['label']), ['ada', 'lulus', 'aktif', 'yes', 'true', '1']);
    }
    ?>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-<?= $boolValue ? 'check' : 'times' ?> text-<?= $boolValue ? 'success' : 'danger' ?>"></i>
            </span>
        </div>
        <input type="text" class="form-control" name="ranges[<?= $range['id'] ?>][label]"
            value="<?= esc($range['label'] ?? '') ?>" required>
        <input type="hidden" name="ranges[<?= $range['id'] ?>][type]" value="boolean">
        <input type="hidden" name="ranges[<?= $range['id'] ?>][value]" value="<?= $boolValue ? 'true' : 'false' ?>">
    </div>

<?php elseif (isset($range['type']) && $range['type'] == 'fixed'): ?>
    <!-- Fixed type ranges -->
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-tag"></i>
            </span>
        </div>
        <input type="text" class="form-control" name="ranges[<?= $range['id'] ?>][label]"
            value="<?= esc($range['label'] ?? '') ?>" required>
        <input type="hidden" name="ranges[<?= $range['id'] ?>][type]" value="fixed">
    </div>

<?php else: ?>
    <!-- Numeric ranges -->
    <?php
    // Determine if this category should use integers
    $useIntegers = in_array($category ?? '', ['integrity', 'discipline']);
    $stepValue = $useIntegers ? '1' : '0.01';
    $inputType = $useIntegers ? 'number' : 'number';
    ?>
    <div class="input-group">
        <?php if (($range['start'] ?? null) === null): ?>
            <!-- Less than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-info">
                    <i class="fas fa-less-than"></i>
                </span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][end]"
                value="<?= $range['end'] ?? '' ?>"
                step="<?= $stepValue ?>"
                <?= $useIntegers ? 'min="0"' : 'min="0"' ?>
                required>
            <input type="hidden" name="ranges[<?= $range['id'] ?>][start]" value="">

        <?php elseif (($range['end'] ?? null) === null): ?>
            <!-- Greater than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-success">
                    <i class="fas fa-greater-than"></i>
                </span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][start]"
                value="<?= $range['start'] ?? '' ?>"
                step="<?= $stepValue ?>"
                <?= $useIntegers ? 'min="0"' : 'min="0"' ?>
                required>
            <input type="hidden" name="ranges[<?= $range['id'] ?>][end]" value="">

        <?php else: ?>
            <!-- Between range -->
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][start]"
                value="<?= $range['start'] ?? '' ?>"
                step="<?= $stepValue ?>"
                <?= $useIntegers ? 'min="0"' : 'min="0"' ?>
                required>
            <div class="input-group-prepend input-group-append">
                <span class="input-group-text">-</span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $range['id'] ?>][end]"
                value="<?= $range['end'] ?? '' ?>"
                step="<?= $stepValue ?>"
                <?= $useIntegers ? 'min="0"' : 'min="0"' ?>
                required>
        <?php endif; ?>

        <?php if ($useIntegers): ?>
            <div class="input-group-append">
                <span class="input-group-text text-muted">
                    <small>bilangan bulat</small>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>