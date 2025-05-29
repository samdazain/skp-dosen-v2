<?php

/**
 * Modals Partial
 * Contains all modal dialogs for score management
 */
?>

<!-- Add Range Modal -->
<div class="modal fade" id="addRangeModal" tabindex="-1" role="dialog" aria-labelledby="addRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addRangeModalLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Rentang Nilai Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('score/add-range') ?>" method="post" id="addRangeForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="category" id="addRangeCategory">
                    <input type="hidden" name="subcategory" id="addRangeSubcategory">

                    <div class="form-group">
                        <label for="rangeType" class="font-weight-bold">
                            <i class="fas fa-list mr-1"></i>
                            Tipe Rentang
                        </label>
                        <select class="form-control" id="rangeType" name="range_type" required>
                            <option value="">-- Pilih Tipe Rentang --</option>
                            <option value="range">Rentang Numerik (A - B)</option>
                            <option value="above">Lebih Dari (> A)</option>
                            <option value="below">Kurang Dari (< A)</option>
                            <option value="fixed">Nilai Tetap</option>
                            <option value="boolean">Boolean (Ya/Tidak)</option>
                        </select>
                    </div>

                    <div id="numericRangeFields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="rangeStartGroup">
                                    <label for="rangeStart">
                                        <i class="fas fa-play mr-1"></i>
                                        Nilai Awal
                                    </label>
                                    <input type="number" class="form-control" id="rangeStart" name="range_start" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="rangeEndGroup">
                                    <label for="rangeEnd">
                                        <i class="fas fa-stop mr-1"></i>
                                        Nilai Akhir
                                    </label>
                                    <input type="number" class="form-control" id="rangeEnd" name="range_end" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="labelGroup">
                        <label for="rangeLabel" class="font-weight-bold">
                            <i class="fas fa-tag mr-1"></i>
                            Label
                        </label>
                        <input type="text" class="form-control" id="rangeLabel" name="range_label" placeholder="Contoh: '1-2', 'Ada', 'Tidak Kooperatif'">
                        <small class="form-text text-muted">
                            Label yang akan ditampilkan untuk rentang ini
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="score" class="font-weight-bold">
                            <i class="fas fa-star mr-1"></i>
                            Nilai (Skor)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="score" name="score" required min="0" max="100" placeholder="0">
                            <div class="input-group-append">
                                <span class="input-group-text">poin</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Nilai yang akan diberikan untuk rentang ini (0-100)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Range Modal -->
<div class="modal fade" id="deleteRangeModal" tabindex="-1" role="dialog" aria-labelledby="deleteRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteRangeModalLabel">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Rentang Nilai
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('score/delete-range') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="range_id" id="deleteRangeId">

                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                    </div>

                    <p class="text-center">
                        Apakah Anda yakin ingin menghapus rentang nilai ini?
                    </p>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Menghapus rentang nilai dapat mempengaruhi perhitungan skor pada data yang ada.
                        Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>