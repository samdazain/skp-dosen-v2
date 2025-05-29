<?php

/**
 * Reusable Export Buttons Component
 * 
 * @param array $exports Array of export configurations
 * Example: [
 *     'excel' => ['url' => 'path/to/excel', 'label' => 'Excel'],
 *     'pdf' => ['url' => 'path/to/pdf', 'label' => 'PDF'],
 *     'csv' => ['url' => 'path/to/csv', 'label' => 'CSV']
 * ]
 */

$exports = $exports ?? [];
$defaultExports = [
    'excel' => [
        'class' => 'btn-success',
        'icon' => 'fas fa-file-excel',
        'label' => 'Excel'
    ],
    'pdf' => [
        'class' => 'btn-danger',
        'icon' => 'fas fa-file-pdf',
        'label' => 'PDF'
    ],
    'csv' => [
        'class' => 'btn-info',
        'icon' => 'fas fa-file-csv',
        'label' => 'CSV'
    ],
    'print' => [
        'class' => 'btn-secondary',
        'icon' => 'fas fa-print',
        'label' => 'Print'
    ]
];
?>

<?php if (!empty($exports)): ?>
    <div class="btn-group mr-2" role="group">
        <?php foreach ($exports as $type => $config): ?>
            <?php
            $defaults = $defaultExports[$type] ?? [
                'class' => 'btn-secondary',
                'icon' => 'fas fa-download',
                'label' => ucfirst($type)
            ];

            $btnClass = $config['class'] ?? $defaults['class'];
            $btnIcon = $config['icon'] ?? $defaults['icon'];
            $btnLabel = $config['label'] ?? $defaults['label'];
            $btnUrl = $config['url'] ?? '#';
            $btnTarget = $config['target'] ?? '_self';
            ?>

            <a href="<?= $btnUrl ?>"
                class="btn btn-sm <?= $btnClass ?>"
                title="Export <?= esc($btnLabel) ?>"
                target="<?= $btnTarget ?>"
                data-toggle="tooltip">
                <i class="<?= $btnIcon ?> mr-1"></i> <?= esc($btnLabel) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>