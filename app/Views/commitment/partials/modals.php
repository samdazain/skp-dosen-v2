<!-- Competency Confirmation Modal -->
<div class="modal fade" id="competencyModal" tabindex="-1" role="dialog" aria-labelledby="competencyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="competencyModalLabel">Konfirmasi Perubahan Status Kompetensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('commitment/update-competency') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="competencyLecturerId">
                    <input type="hidden" name="status" id="competencyStatus">
                    <p id="competencyConfirmText">Apakah Anda yakin ingin mengubah status kompetensi dosen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetCompetencyRadio()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tri Dharma Confirmation Modal -->
<div class="modal fade" id="triDharmaModal" tabindex="-1" role="dialog" aria-labelledby="triDharmaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="triDharmaModalLabel">Konfirmasi Perubahan Status Tri Dharma</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('commitment/update-tri-dharma') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="lecturer_id" id="triDharmaLecturerId">
                    <input type="hidden" name="status" id="triDharmaStatus">
                    <p id="triDharmaConfirmText">Apakah Anda yakin ingin mengubah status Tri Dharma dosen ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="resetTriDharmaRadio()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>