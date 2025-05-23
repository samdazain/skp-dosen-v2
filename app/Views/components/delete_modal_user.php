<?php

/**
 * Reusable delete confirmation modal
 * 
 * @var string $title Modal title
 * @var string $messagePrefix Message prefix before the item name
 */
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
                <p><?= $messagePrefix ?? 'Apakah Anda yakin ingin menghapus' ?> <strong id="delete-item-name"></strong>?
                </p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <form action="" method="post" id="delete-form">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>