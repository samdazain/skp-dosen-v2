<?php

/**
 * Discipline Data Table Partial
 * 
 * @var array $disciplineData
 */
?>

<div class="card shadow">
    <?= view('discipline/partials/table_header') ?>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped" id="disciplineTable">
            <?= view('discipline/partials/table_thead') ?>
            <?= view('discipline/partials/table_tbody') ?>
        </table>
    </div>

    <?php if (!empty($disciplineData)): ?>
        <?= view('discipline/partials/table_footer') ?>
    <?php endif; ?>
</div>

<?= view('discipline/partials/table_styles') ?>
<?= view('discipline/partials/scripts') ?>