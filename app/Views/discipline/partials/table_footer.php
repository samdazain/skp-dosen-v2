<?php

/**
 * Table Footer Component
 * 
 * @var array $disciplineData
 */
?>

<div class="card-footer">
    <div class="float-left">
        <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Menampilkan <?= count($disciplineData) ?> data dosen.
            Data disiplin dikelola melalui upload Excel.
            Nilai dihitung otomatis berdasarkan konfigurasi scoring.
        </small>
    </div>

    <!-- Pagination -->
    <?php // view('discipline/partials/pagination') 
    ?>
</div>