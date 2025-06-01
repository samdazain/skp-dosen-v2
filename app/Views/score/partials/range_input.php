<?php

/**
 * Range Input Partial
 * Handles different types of range inputs (numeric, boolean, fixed)
 * 
 * @var array $range
 * @var string $fieldType
 */
?>

<?php
$rangeType = $range['type'] ?? 'range';
$rangeStart = $range['start'] ?? null;
$rangeEnd = $range['end'] ?? null;
$rangeLabel = $range['label'] ?? '';
$rangeId = $range['id'] ?? '';

// Determine if this category should use integers
$useIntegers = in_array($category ?? '', ['integrity', 'discipline']);
$stepValue = $useIntegers ? '1' : '0.01';
$inputType = 'number';
?>

<?php if ($rangeType === 'boolean'): ?>
    <!-- Boolean type ranges -->
    <?php
    $boolValue = in_array(strtolower($rangeLabel), ['ada', 'lulus', 'aktif', 'yes', 'true', '1', 'pass']);
    ?>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-<?= $boolValue ? 'check' : 'times' ?> text-<?= $boolValue ? 'success' : 'danger' ?>"></i>
            </span>
        </div>
        <input type="text" class="form-control"
            name="ranges[<?= $rangeId ?>][label]"
            value="<?= esc($rangeLabel) ?>"
            placeholder="Contoh: Ada, Lulus, Aktif"
            required>
        <input type="hidden" name="ranges[<?= $rangeId ?>][type]" value="boolean">
    </div>

<?php elseif ($rangeType === 'fixed'): ?>
    <!-- Fixed type ranges -->
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-tag"></i>
            </span>
        </div>
        <input type="text" class="form-control"
            name="ranges[<?= $rangeId ?>][label]"
            value="<?= esc($rangeLabel) ?>"
            placeholder="Contoh: Tidak Kooperatif, Sangat Baik"
            required>
        <input type="hidden" name="ranges[<?= $rangeId ?>][type]" value="fixed">
    </div>

<?php else: ?>
    <!-- Numeric ranges -->
    <div class="input-group">
        <?php if ($rangeStart === null && $rangeEnd !== null): ?>
            <!-- Less than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-info">
                    <i class="fas fa-less-than"></i>
                </span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $rangeId ?>][end]"
                value="<?= $rangeEnd ?>"
                step="<?= $stepValue ?>"
                min="0"
                placeholder="Nilai maksimum"
                required>
            <input type="hidden" name="ranges[<?= $rangeId ?>][start]" value="">

        <?php elseif ($rangeStart !== null && $rangeEnd === null): ?>
            <!-- Greater than range -->
            <div class="input-group-prepend">
                <span class="input-group-text text-success">
                    <i class="fas fa-greater-than"></i>
                </span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $rangeId ?>][start]"
                value="<?= $rangeStart ?>"
                step="<?= $stepValue ?>"
                min="0"
                placeholder="Nilai minimum"
                required>
            <input type="hidden" name="ranges[<?= $rangeId ?>][end]" value="">

        <?php elseif ($rangeStart === $rangeEnd): ?>
            <!-- Exact value -->
            <div class="input-group-prepend">
                <span class="input-group-text text-secondary">
                    <i class="fas fa-equals"></i>
                </span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $rangeId ?>][start]"
                value="<?= $rangeStart ?>"
                step="<?= $stepValue ?>"
                min="0"
                placeholder="Nilai tepat"
                required>
            <input type="hidden" name="ranges[<?= $rangeId ?>][end]" value="<?= $rangeEnd ?>">

        <?php else: ?>
            <!-- Between range -->
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $rangeId ?>][start]"
                value="<?= $rangeStart ?>"
                step="<?= $stepValue ?>"
                min="0"
                placeholder="Dari"
                required>
            <div class="input-group-prepend input-group-append">
                <span class="input-group-text">-</span>
            </div>
            <input type="<?= $inputType ?>"
                class="form-control"
                name="ranges[<?= $rangeId ?>][end]"
                value="<?= $rangeEnd ?>"
                step="<?= $stepValue ?>"
                min="0"
                placeholder="Sampai"
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
    <input type="hidden" name="ranges[<?= $rangeId ?>][type]" value="<?= $rangeType ?>">
<?php endif; ?>