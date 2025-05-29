<?php
$message = $message ?? 'Tidak ada data';
$searchTerm = $searchTerm ?? '';
$columns = $columns ?? [];
$colCount = count($columns);
?>

<tr>
    <td colspan="<?= $colCount ?>" class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-database fa-4x text-muted mb-3"></i>
            <h5 class="text-muted"><?= esc($message) ?></h5>

            <?php if (!empty($searchTerm)): ?>
                <p class="text-muted">
                    Tidak ditemukan hasil untuk pencarian
                    "<strong><?= esc($searchTerm) ?></strong>"
                </p>
                <div class="mt-3">
                    <a href="<?= current_url() ?>" class="btn btn-sm btn-secondary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Semua Data
                    </a>
                </div>
            <?php else: ?>
                <p class="text-muted">Belum ada data yang tersedia</p>
            <?php endif; ?>
        </div>
    </td>
</tr>