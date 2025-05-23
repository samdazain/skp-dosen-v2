<?php

/**
 * Semester Selector Component
 * 
 * @var array $semesters List of available semesters
 * @var array $activeSemester Currently active semester
 */

// If $semesters is not provided, use empty array
$semesters = $semesters ?? [];

// Get active semester ID from session
$activeSemesterId = session()->get('activeSemesterId');

// Get current URI for redirect after semester change
$currentUri = current_url(true)->getPath();
?>

<div class="semester-selector dropdown">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="semesterDropdown" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-calendar-alt mr-1"></i>
        <?= session()->get('activeSemesterText') ?? 'Pilih Semester' ?>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="semesterDropdown">
        <h6 class="dropdown-header">Pilih Semester Aktif</h6>

        <?php foreach ($semesters as $semester): ?>
            <?php
            $isActive = ($activeSemesterId == $semester['id']);
            $termText = ($semester['term'] == '1') ? 'Ganjil' : 'Genap';
            $yearRange = $semester['year'] . '/' . ($semester['year'] + 1);
            $semesterText = "Semester $termText $yearRange";
            ?>

            <a class="dropdown-item <?= $isActive ? 'active' : '' ?>"
                href="<?= base_url('semester/change?semester_id=' . $semester['id'] . '&redirect=' . urlencode($currentUri)) ?>"
                data-semester-id="<?= $semester['id'] ?>" data-semester-text="<?= $semesterText ?>">
                <?= $semesterText ?>
                <?php if ($isActive): ?>
                    <i class="fas fa-check ml-2"></i>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>