<?php

/**
 * Export Buttons Component
 */
?>

<div class="btn-group mr-3">
    <a href="<?= base_url('discipline/export-excel') ?>"
        class="btn btn-sm btn-success"
        data-toggle="tooltip"
        title="Export data ke Excel">
        <i class="fas fa-file-excel mr-1"></i> Export Excel
    </a>
    <a href="<?= base_url('discipline/export-pdf') ?>"
        class="btn btn-sm btn-danger"
        data-toggle="tooltip"
        title="Export data ke PDF">
        <i class="fas fa-file-pdf mr-1"></i> Export PDF
    </a>
</div>