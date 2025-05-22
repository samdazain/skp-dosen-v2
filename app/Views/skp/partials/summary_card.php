<?php

/**
 * Summary Card Component
 * 
 * Required parameters:
 * @param string $title - The title of the card (e.g., "Sangat Baik")
 * @param string $icon - Font Awesome icon class (e.g., "fas fa-thumbs-up")
 * @param string $color - Bootstrap color class (e.g., "success", "primary", "warning", "danger")
 * @param int $count - The count to display
 * @param int $percentage - The percentage value for the progress bar
 * @param string $description - Optional description text (default: "dari total dosen")
 */

// Set defaults for optional parameters
$description = $description ?? 'dari total dosen';
?>

<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box bg-gradient-<?= $color ?>">
        <span class="info-box-icon"><i class="<?= $icon ?>"></i></span>
        <div class="info-box-content">
            <span class="info-box-text"><?= $title ?></span>
            <span class="info-box-number"><?= $count ?></span>
            <div class="progress">
                <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
            </div>
            <span class="progress-description"><?= $percentage ?>% <?= $description ?></span>
        </div>
    </div>
</div>