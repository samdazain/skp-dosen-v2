<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <?= session('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= session('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?= session('warning') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->has('info')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        <?= session('info') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Auto-calculation notification -->
<?php if (isset($calculationResult) && ($calculationResult['added'] > 0 || $calculationResult['updated'] > 0)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-sync-alt mr-2"></i>
        <strong>Auto-Kalkulasi Berhasil:</strong>
        <?php if ($calculationResult['added'] > 0): ?>
            Menambahkan <?= $calculationResult['added'] ?> record baru.
        <?php endif; ?>
        <?php if ($calculationResult['updated'] > 0): ?>
            Memperbarui <?= $calculationResult['updated'] ?> nilai orientasi pelayanan.
        <?php endif; ?>
        <small class="ml-2 text-muted">(<?= $calculationResult['timestamp'] ?? date('Y-m-d H:i:s') ?>)</small>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>