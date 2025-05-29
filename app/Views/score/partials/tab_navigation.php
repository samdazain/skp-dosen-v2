<?php

/**
 * Tab Navigation Partial
 * 
 * @var array $scoreRanges
 */
?>

<ul class="nav nav-tabs" id="score-tab" role="tablist">
    <?php
    $isFirst = true;
    foreach ($scoreRanges as $key => $category):
    ?>
        <li class="nav-item">
            <a class="nav-link <?= $isFirst ? 'active' : '' ?>"
                id="<?= $key ?>-tab"
                data-toggle="pill"
                href="#<?= $key ?>-content"
                role="tab"
                aria-controls="<?= $key ?>-content"
                aria-selected="<?= $isFirst ? 'true' : 'false' ?>">
                <i class="fas fa-<?= getTabIcon($key) ?> mr-2"></i>
                <?= $category['title'] ?>
            </a>
        </li>
    <?php
        $isFirst = false;
    endforeach;
    ?>
</ul>

<?php
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