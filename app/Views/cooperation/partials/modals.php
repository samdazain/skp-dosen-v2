<!-- Cooperation Level Change Confirmation Modal -->
<div class="modal fade" id="cooperationModal" tabindex="-1" role="dialog" aria-labelledby="cooperationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cooperationModalLabel">
                    <i class="fas fa-handshake mr-2"></i>
                    Konfirmasi Perubahan Tingkat Kerja Sama
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="cooperationForm">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="cooperationLecturerId">
                    <input type="hidden" name="level" id="cooperationLevel">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Perhatian:</strong> Perubahan tingkat kerja sama akan otomatis menghitung ulang skor
                        dosen.
                    </div>

                    <p id="cooperationConfirmText">Apakah Anda yakin ingin mengubah tingkat kerja sama dosen ini?</p>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Dosen:</strong>
                            <p class="text-muted mb-1" id="cooperationLecturerName">-</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Tingkat Baru:</strong>
                            <p class="text-muted mb-1" id="cooperationNewLevel">-</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Skor Saat Ini:</strong>
                            <p class="text-muted mb-1" id="cooperationCurrentScore">-</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Skor Baru:</strong>
                            <p class="text-primary mb-1" id="cooperationNewScore">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetCooperationRadio()">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header" id="notificationHeader">
                <h5 class="modal-title" id="notificationModalLabel">
                    <i id="notificationIcon" class="fas fa-check mr-2"></i>
                    <span id="notificationTitle">Berhasil</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p id="notificationMessage">Perubahan berhasil disimpan</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Confirmation Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog" aria-labelledby="bulkUpdateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUpdateModalLabel">
                    <i class="fas fa-users mr-2"></i>
                    Konfirmasi Perubahan Massal
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Peringatan:</strong> Anda akan mengubah tingkat kerja sama untuk beberapa dosen sekaligus.
                </div>

                <p>Apakah Anda yakin ingin melakukan perubahan massal ini?</p>

                <div id="bulkUpdateSummary"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" id="confirmBulkUpdate">
                    <i class="fas fa-check mr-1"></i> Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mb-0" id="loadingMessage">Memproses perubahan...</p>
            </div>
        </div>
    </div>
</div>

<!-- Data Refresh Modal -->
<div class="modal fade" id="refreshModal" tabindex="-1" role="dialog" aria-labelledby="refreshModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refreshModalLabel">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Data Kerja Sama
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Proses refresh akan:
                    <ul class="mb-0 mt-2">
                        <li>Memperbarui data dari tabel dosen</li>
                        <li>Menambahkan record baru untuk dosen yang belum ada</li>
                        <li>Menghitung ulang semua skor secara otomatis</li>
                    </ul>
                </div>

                <p>Apakah Anda ingin melanjutkan proses refresh data?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="button" class="btn btn-info" onclick="performDataRefresh()">
                    <i class="fas fa-sync-alt mr-1"></i> Ya, Refresh Data
                </button>
            </div>
        </div>
    </div>
</div>