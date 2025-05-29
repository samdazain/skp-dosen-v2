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
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Rentang</th>
                <th style="width: 25%">Nilai</th>
                <th style="width: 30%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ranges as $index => $range): ?>
                <tr data-range-id="<?= $range['id'] ?>" class="range-row">
                    <td class="text-center">
                        <span class="badge badge-secondary"><?= $index + 1 ?></span>
                    </td>
                    <td>
                        <?= view('score/partials/range_input', [
                            'range' => $range,
                            'fieldType' => 'range'
                        ]) ?>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" class="form-control text-center" name="ranges[<?= $range['id'] ?>][score]"
                                value="<?= $range['score'] ?>" required min="0" max="100">
                            <div class="input-group-append">
                                <span class="input-group-text">pts</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-range-btn"
                                data-toggle="tooltip" title="Edit" data-range-id="<?= $range['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-range" data-toggle="modal"
                                data-target="#deleteRangeModal" data-range-id="<?= $range['id'] ?>" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <td colspan="4" class="text-center">
                    <button type="button" class="btn btn-sm btn-success add-range" data-toggle="modal"
                        data-target="#addRangeModal" data-category="<?= $category ?>"
                        data-subcategory="<?= $subcategory ?>">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Rentang Nilai
                    </button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>