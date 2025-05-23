<?php
?>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel"><?= $title ?? 'Konfirmasi Hapus' ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= $messagePrefix ?? 'Apakah Anda yakin ingin menghapus' ?> <span id="delete-item-name"
                        class="font-weight-bold"></span>?</p>
                <p class="text-danger"><?= $warning ?? 'Tindakan ini tidak dapat dibatalkan.' ?></p>
            </div>
            <div class="modal-footer">
                <form id="delete-form" action="" method="post">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= $cancelText ?? 'Batal' ?></button>
                    <button type="submit" class="btn btn-danger"><?= $confirmText ?? 'Hapus' ?></button>
                </form>
            </div>
        </div>
    </div>
</div>