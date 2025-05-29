<?php

/**
 * Range Table Partial
 * 
 * @var array $ranges
 * @var string $category
 * @var string $subcategory
 * @var string $subcategoryTitle
 */
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover range-table">
        <thead class="thead-light">
            <tr>
                <th width="35%" class="text-center">
                    <i class="fas fa-rulers mr-1"></i>
                    Rentang Nilai
                </th>
                <th width="20%" class="text-center">
                    <i class="fas fa-star mr-1"></i>
                    Skor
                </th>
                <th width="25%" class="text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Jenis
                </th>
                <th width="20%" class="text-center">
                    <i class="fas fa-cogs mr-1"></i>
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ranges)): ?>
                <?php foreach ($ranges as $range): ?>
                    <tr class="range-row" data-range-id="<?= $range['id'] ?>">
                        <td>
                            <?= view('score/partials/range_input', [
                                'range' => [
                                    'id' => $range['id'],
                                    'start' => $range['start'] ?? null,
                                    'end' => $range['end'] ?? null,
                                    'label' => $range['label'] ?? '',
                                    'type' => $range['type'] ?? 'range',
                                    'value' => $range['value'] ?? null
                                ],
                                'category' => $category,
                                'fieldType' => 'range'
                            ]) ?>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control text-center score-input"
                                    name="ranges[<?= $range['id'] ?>][score]" value="<?= (int)$range['score'] ?>" required
                                    min="0" max="100" step="1" data-range-id="<?= $range['id'] ?>"
                                    data-category="<?= $category ?>" data-subcategory="<?= $subcategory ?>"
                                    <?= isset($range['editable']) && !$range['editable'] ? 'readonly' : '' ?>>
                                <div class="input-group-append">
                                    <span class="input-group-text">pts</span>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Nilai bilangan bulat (tidak ada desimal)
                            </small>
                        </td>
                        <td class="text-center">
                            <?php
                            $typeLabels = [
                                'range' => ['Rentang', 'fas fa-arrows-alt-h', 'primary'],
                                'above' => ['Lebih dari', 'fas fa-chevron-up', 'success'],
                                'below' => ['Kurang dari', 'fas fa-chevron-down', 'info'],
                                'exact' => ['Tepat', 'fas fa-equals', 'secondary'],
                                'fixed' => ['Tetap', 'fas fa-lock', 'warning'],
                                'boolean' => ['Boolean', 'fas fa-toggle-on', 'dark']
                            ];
                            $typeInfo = $typeLabels[$range['type']] ?? ['Unknown', 'fas fa-question', 'light'];
                            ?>
                            <span class="badge badge-<?= $typeInfo[2] ?> p-2">
                                <i class="<?= $typeInfo[1] ?> mr-1"></i>
                                <?= $typeInfo[0] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if (isset($range['editable']) && $range['editable']): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-range"
                                    data-range-id="<?= $range['id'] ?>" data-toggle="modal" data-target="#deleteRangeModal"
                                    title="Hapus rentang nilai">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-lock mr-1"></i>
                                    Sistem
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <br>
                        Belum ada rentang nilai untuk <?= esc($subcategoryTitle) ?>
                        <br>
                        <button type="button" class="btn btn-primary btn-sm mt-2 add-range" data-category="<?= $category ?>"
                            data-subcategory="<?= $subcategory ?>" data-toggle="modal" data-target="#addRangeModal">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Rentang Nilai
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($ranges)): ?>
        <div class="mt-2">
            <button type="button" class="btn btn-outline-primary btn-sm add-range" data-category="<?= $category ?>"
                data-subcategory="<?= $subcategory ?>" data-toggle="modal" data-target="#addRangeModal">
                <i class="fas fa-plus mr-1"></i>
                Tambah Rentang Nilai
            </button>
        </div>
    <?php endif; ?>
</div>