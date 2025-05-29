<?php

/**
 * Content Header with Breadcrumbs and Semester Selector
 * 
 * @var string $header_title Page title
 * @var array $breadcrumbs Breadcrumb items
 * @var bool $show_semester_selector Whether to display semester selector (default: true)
 */

// Load SemesterModel to get available semesters
$semesterModel = new \App\Models\SemesterModel();
$semesters = $semesterModel->getAllSemesters();

// Get active semester from session or set the latest one as active
$activeSemesterId = session()->get('activeSemesterId');
$activeSemester = null;

if ($activeSemesterId) {
    $activeSemester = $semesterModel->getSemesterById($activeSemesterId);
} else {
    $activeSemester = $semesterModel->getCurrentSemester();
    if ($activeSemester) {
        session()->set('activeSemesterId', $activeSemester['id']);
        session()->set('activeSemesterText', $semesterModel->formatSemester($activeSemester));
    }
}

// Default to showing semester selector unless explicitly set to false
$showSemesterSelector = $show_semester_selector ?? true;
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $header_title ?? 'Page Title' ?></h1>
            </div>
            <div class="col-sm-6">
                <div class="d-flex justify-content-end align-items-center">
                    <?php if ($showSemesterSelector && !empty($semesters)): ?>
                        <div class="mr-3">
                            <?= view('components/semester_selector', [
                                'semesters' => $semesters,
                                'activeSemester' => $activeSemester,
                                'semesterModel' => $semesterModel
                            ]) ?>
                        </div>
                    <?php endif; ?>

                    <ol class="breadcrumb float-sm-right mb-0">
                        <?php foreach ($breadcrumbs ?? [] as $item): ?>
                            <?php if (isset($item['active']) && $item['active']): ?>
                                <li class="breadcrumb-item active"><?= $item['text'] ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= base_url($item['url'] ?? '#') ?>"><?= $item['text'] ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>