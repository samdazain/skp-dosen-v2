<?php

/**
 * Tab Navigation Partial
 * 
 * @var array $scoreRanges
 */

// Define category titles
$categoryTitles = [
    'integrity' => 'Integritas',
    'discipline' => 'Disiplin',
    'commitment' => 'Komitmen',
    'cooperation' => 'Kerjasama',
    'orientation' => 'Orientasi Pelayanan'
];

// Group score ranges by category
$categories = [];
if (!empty($scoreRanges) && is_array($scoreRanges)) {
    foreach ($scoreRanges as $range) {
        if (is_array($range) && isset($range['category'])) {
            $category = $range['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $range;
        }
    }
}

// If no categories found, show default empty categories
if (empty($categories)) {
    foreach ($categoryTitles as $key => $title) {
        $categories[$key] = [];
    }
}

/**
 * Helper function to get appropriate icon for each tab
 */
function getTabIcon($key)
{
    $icons = [
        'integrity' => 'user-shield',
        'discipline' => 'clock',
        'commitment' => 'handshake',
        'cooperation' => 'users',
        'orientation' => 'compass'
    ];
    return $icons[$key] ?? 'cog';
}
?>

<ul class="nav nav-tabs mb-3" id="score-tab" role="tablist">
    <?php
    $isFirst = true;
    foreach ($categories as $categoryKey => $ranges):
        $title = $categoryTitles[$categoryKey] ?? ucfirst($categoryKey);
    ?>
        <li class="nav-item">
            <a class="nav-link <?= $isFirst ? 'active' : '' ?>"
                id="<?= $categoryKey ?>-tab"
                data-toggle="tab"
                href="#<?= $categoryKey ?>-content"
                role="tab"
                aria-controls="<?= $categoryKey ?>-content"
                aria-selected="<?= $isFirst ? 'true' : 'false' ?>">
                <i class="fas fa-<?= getTabIcon($categoryKey) ?> mr-2"></i>
                <?= esc($title) ?>
                <span class="badge badge-secondary ml-2"><?= count($ranges) ?></span>
            </a>
        </li>
    <?php
        $isFirst = false;
    endforeach;
    ?>
</ul>