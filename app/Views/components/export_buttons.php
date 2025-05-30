<?php

/**
 * Export Buttons Component
 * 
 * @var string $baseUrl Base URL for export routes
 * @var array $exportTypes Array of export types ('excel', 'pdf')
 * @var string $btnSize Button size class (default: 'btn-sm')
 * @var bool $showGroup Whether to group buttons (default: true)
 */

$baseUrl = $baseUrl ?? '';
$exportTypes = $exportTypes ?? ['excel', 'pdf'];
$btnSize = $btnSize ?? 'btn-sm';
$showGroup = $showGroup ?? true;

// Define export configurations
$exportConfigs = [
    'excel' => [
        'icon' => 'fas fa-file-excel',
        'class' => 'btn-success',
        'text' => 'Export Excel',
        'url' => $baseUrl . '/export-excel'
    ],
    'pdf' => [
        'icon' => 'fas fa-file-pdf',
        'class' => 'btn-danger',
        'text' => 'Export PDF',
        'url' => $baseUrl . '/export-pdf'
    ],
    'csv' => [
        'icon' => 'fas fa-file-csv',
        'class' => 'btn-info',
        'text' => 'Export CSV',
        'url' => $baseUrl . '/export-csv'
    ]
];
?>

<?php if ($showGroup): ?>
    <div class="btn-group mr-2" role="group" aria-label="Export options">
    <?php endif; ?>

    <?php foreach ($exportTypes as $type): ?>
        <?php if (isset($exportConfigs[$type])): ?>
            <?php $config = $exportConfigs[$type]; ?>
            <a href="<?= base_url($config['url']) ?>" class="btn <?= $config['class'] ?> <?= $btnSize ?>"
                title="<?= $config['text'] ?>" data-toggle="tooltip">
                <i class="<?= $config['icon'] ?> mr-1"></i>
                <?= $config['text'] ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($showGroup): ?>
    </div>
<?php endif; ?>

<script>
    // Initialize tooltips for export buttons
    document.addEventListener('DOMContentLoaded', function() {
        const exportButtons = document.querySelectorAll('[data-toggle="tooltip"]');
        exportButtons.forEach(button => {
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $(button).tooltip();
            }
        });
    });
</script>