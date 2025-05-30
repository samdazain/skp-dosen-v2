<?php

/**
 * Table Body Component
 * 
 * @var array $disciplineData
 */

// Study program mapping
$studyProgramMap = [
    'bisnis_digital' => 'Bisnis Digital',
    'informatika' => 'Informatika',
    'sistem_informasi' => 'Sistem Informasi',
    'sains_data' => 'Sains Data',
    'magister_teknologi_informasi' => 'Magister Teknologi Informasi',
    null => 'Dekanat'
];

/**
 * Get score styling based on score value
 */
function getScoreStyles($score): array
{
    if ($score >= 90) {
        return [
            'scoreClass' => 'text-success',
            'scoreLabel' => 'Sangat Baik',
            'badgeClass' => 'badge-success'
        ];
    } elseif ($score >= 80) {
        return [
            'scoreClass' => 'text-primary',
            'scoreLabel' => 'Baik',
            'badgeClass' => 'badge-primary'
        ];
    } elseif ($score >= 70) {
        return [
            'scoreClass' => 'text-warning',
            'scoreLabel' => 'Cukup',
            'badgeClass' => 'badge-warning'
        ];
    } else {
        return [
            'scoreClass' => 'text-danger',
            'scoreLabel' => 'Kurang',
            'badgeClass' => 'badge-danger'
        ];
    }
}
?>

<tbody>
    <?php if (!empty($disciplineData)): ?>
        <?php foreach ($disciplineData as $index => $lecturer): ?>
            <?php
            $score = (int)$lecturer['score'];
            $scoreStyles = getScoreStyles($score);
            $displayProgram = $studyProgramMap[$lecturer['study_program']] ?? $lecturer['study_program'];
            ?>
            <tr data-lecturer-id="<?= $lecturer['lecturer_id'] ?>" data-position="<?= esc($lecturer['position']) ?>"
                data-study-program="<?= esc($lecturer['study_program']) ?>">

                <!-- Row Number -->
                <td class="text-center"><?= $index + 1 ?></td>

                <!-- Lecturer Info -->
                <td>
                    <?= view('discipline/partials/lecturer_info_cell', [
                        'lecturer' => $lecturer
                    ]) ?>
                </td>

                <!-- Study Program -->
                <td>
                    <span class="badge badge-info"><?= esc($displayProgram) ?></span>
                </td>

                <!-- Absence Data -->
                <td class="text-center">
                    <span class="discipline-value" data-field="daily_absence">
                        <?= (int)$lecturer['daily_absence'] ?>
                    </span>
                </td>
                <td class="text-center">
                    <span class="discipline-value" data-field="exercise_absence">
                        <?= (int)$lecturer['exercise_morning_absence'] ?>
                    </span>
                </td>
                <td class="text-center">
                    <span class="discipline-value" data-field="ceremony_absence">
                        <?= (int)$lecturer['ceremony_absence'] ?>
                    </span>
                </td>

                <!-- Score -->
                <td class="text-center">
                    <span class="badge badge-lg font-weight-bold <?= $scoreStyles['scoreClass'] ?>">
                        <?= $score ?>
                    </span>
                </td>

                <!-- Status -->
                <td class="text-center">
                    <span class="badge <?= $scoreStyles['badgeClass'] ?>">
                        <?= $scoreStyles['scoreLabel'] ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <?= view('discipline/partials/empty_state_row') ?>
    <?php endif; ?>
</tbody>