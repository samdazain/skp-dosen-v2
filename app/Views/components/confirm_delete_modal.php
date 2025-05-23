<?php
?>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel"><?= $title ?? 'Konfirmasi Hapus' ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= $message ?? 'Anda yakin ingin menghapus data ini?' ?></p>
                <p class="text-danger"><small><?= $warning ?? 'Tindakan ini tidak dapat dibatalkan.' ?></small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal"><?= $cancel_text ?? 'Batal' ?></button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger"><?= $confirm_text ?? 'Hapus' ?></a>
            </div>
        </div>
    </div>
</div>