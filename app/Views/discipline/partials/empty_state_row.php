<?php

/**
 * Empty State Row Component
 */
?>

<tr>
    <td colspan="8" class="text-center py-4">
        <div class="empty-state">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Data</h5>
            <p class="text-muted">Data disiplin untuk semester ini belum tersedia.</p>
            <a href="<?= base_url('upload') ?>" class="btn btn-primary">
                <i class="fas fa-upload mr-1"></i>
                Upload Data
            </a>
        </div>
    </td>
</tr>