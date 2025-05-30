<?php

/**
 * Study Program Badge Component
 * 
 * @var string $program Study program code
 */

$program = $program ?? '';

// Define study program configurations
$programConfigs = [
    'bisnis_digital' => [
        'name' => 'Bisnis Digital',
        'class' => 'badge-primary',
        'icon' => 'fas fa-chart-line'
    ],
    'informatika' => [
        'name' => 'Informatika',
        'class' => 'badge-info',
        'icon' => 'fas fa-code'
    ],
    'sistem_informasi' => [
        'name' => 'Sistem Informasi',
        'class' => 'badge-success',
        'icon' => 'fas fa-database'
    ],
    'sains_data' => [
        'name' => 'Sains Data',
        'class' => 'badge-warning',
        'icon' => 'fas fa-chart-bar'
    ],
    'magister_teknologi_informasi' => [
        'name' => 'Magister TI',
        'class' => 'badge-dark',
        'icon' => 'fas fa-graduation-cap'
    ]
];

$config = $programConfigs[$program] ?? [
    'name' => 'Dekanat',
    'class' => 'badge-danger',
    'icon' => 'fas fa-university'
];
?>

<span class="badge <?= $config['class'] ?> p-2" title="<?= $config['name'] ?>">
    <i class="<?= $config['icon'] ?> mr-1"></i>
    <?= $config['name'] ?>
</span>