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

<div class="col-lg-3 col-6">
    <div class="small-box bg-<?= $color ?>">
        <div class="inner">
            <h3><?= $count ?></h3>
            <p><?= $title ?></p>
            <div class="progress">
                <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
            </div>
            <span class="progress-description">
                <?= $percentage ?>% dari total
            </span>
        </div>
        <div class="icon">
            <i class="<?= $icon ?>"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="filterBySKPCategory('<?= strtolower(str_replace(' ', '_', $title)) ?>')">
            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<script>
    function filterBySKPCategory(category) {
        // This function can be used to filter the table by SKP category
        const categoryMap = {
            'sangat_baik': 'Sangat Baik',
            'baik': 'Baik',
            'cukup': 'Cukup',
            'kurang': 'Kurang'
        };

        const categoryValue = categoryMap[category];
        if (categoryValue) {
            // Update the filter and submit form
            $('#skp_category').val(categoryValue).trigger('change');
        }
    }
</script>