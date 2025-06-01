<?php
// Define category titles and subcategories
$categoryTitles = [
    'integrity' => 'Integritas',
    'discipline' => 'Disiplin',
    'commitment' => 'Komitmen',
    'cooperation' => 'Kerjasama',
    'orientation' => 'Orientasi Pelayanan'
];

$subcategoryTitles = [
    'integrity' => [
        'teaching_attendance' => 'Kehadiran Mengajar',
        'courses_taught' => 'Mata Kuliah yang Diampu'
    ],
    'discipline' => [
        'daily_attendance' => 'Kehadiran Harian',
        'morning_exercise' => 'Senam Pagi',
        'ceremony_attendance' => 'Upacara'
    ],
    'commitment' => [
        'competency' => 'Kompetensi',
        'tri_dharma' => 'Tri Dharma'
    ],
    'cooperation' => [
        'cooperation_level' => 'Level Kerjasama'
    ],
    // 'orientation' => [
    //     'service_quality' => 'Kualitas Pelayanan'
    // ]
];

// Group score ranges by category and subcategory
$organizedRanges = [];
if (!empty($scoreRanges) && is_array($scoreRanges)) {
    foreach ($scoreRanges as $range) {
        if (is_array($range) && isset($range['category']) && isset($range['subcategory'])) {
            $category = $range['category'];
            $subcategory = $range['subcategory'];

            if (!isset($organizedRanges[$category])) {
                $organizedRanges[$category] = [];
            }
            if (!isset($organizedRanges[$category][$subcategory])) {
                $organizedRanges[$category][$subcategory] = [];
            }
            $organizedRanges[$category][$subcategory][] = $range;
        }
    }
}

// Ensure all categories and subcategories exist
foreach ($categoryTitles as $categoryKey => $categoryTitle) {
    if (!isset($organizedRanges[$categoryKey])) {
        $organizedRanges[$categoryKey] = [];
    }

    if (isset($subcategoryTitles[$categoryKey])) {
        foreach ($subcategoryTitles[$categoryKey] as $subKey => $subTitle) {
            if (!isset($organizedRanges[$categoryKey][$subKey])) {
                $organizedRanges[$categoryKey][$subKey] = [];
            }
        }
    }
}
?>

<div class="tab-content" id="score-tabContent">
    <?php
    $isFirst = true;
    foreach ($organizedRanges as $categoryKey => $subcategories):
        $categoryTitle = $categoryTitles[$categoryKey] ?? ucfirst($categoryKey);
    ?>
        <div class="tab-pane fade <?= $isFirst ? 'show active' : '' ?>" id="<?= $categoryKey ?>-content" role="tabpanel"
            aria-labelledby="<?= $categoryKey ?>-tab">

            <div class="mb-3">
                <h4 class="text-primary">
                    <i class="fas fa-cogs mr-2"></i>
                    <?= esc($categoryTitle) ?>
                </h4>
                <hr class="border-primary">
            </div>

            <?php if (empty($subcategories)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Belum ada pengaturan untuk kategori <?= esc($categoryTitle) ?>.
                    <button type="button" class="btn btn-primary btn-sm ml-2 add-range" data-category="<?= $categoryKey ?>"
                        data-subcategory="default" data-toggle="modal" data-target="#addRangeModal">
                        <i class="fas fa-plus mr-1"></i>
                        Tambah Pengaturan
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($subcategories as $subKey => $ranges): ?>
                    <?php
                    $subcategoryTitle = $subcategoryTitles[$categoryKey][$subKey] ?? ucfirst(str_replace('_', ' ', $subKey));
                    ?>
                    <?= view('score/partials/subcategory_card', [
                        'category' => $categoryKey,
                        'subKey' => $subKey,
                        'subcategory' => [
                            'title' => $subcategoryTitle,
                            'ranges' => $ranges
                        ]
                    ]) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php
        $isFirst = false;
    endforeach;
    ?>
</div>