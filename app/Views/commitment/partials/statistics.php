<div class="row mt-4">
    <!-- Competency Stats -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    Status Kompetensi
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Aktif</span>
                                <span class="info-box-number"><?= $stats['active_competence'] ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $stats['active_competence_percentage'] ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= $stats['active_competence_percentage'] ?>% dari total dosen
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tidak Aktif</span>
                                <span class="info-box-number"><?= $stats['inactive_competence'] ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= 100 - $stats['active_competence_percentage'] ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= 100 - $stats['active_competence_percentage'] ?>% dari total dosen
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tri Dharma Stats -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-university mr-1"></i>
                    Status Tri Dharma
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Lulus</span>
                                <span class="info-box-number"><?= $stats['pass_tri_dharma'] ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $stats['pass_tri_dharma_percentage'] ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= $stats['pass_tri_dharma_percentage'] ?>% dari total dosen
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tidak Lulus</span>
                                <span class="info-box-number"><?= $stats['fail_tri_dharma'] ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= 100 - $stats['pass_tri_dharma_percentage'] ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    <?= 100 - $stats['pass_tri_dharma_percentage'] ?>% dari total dosen
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Position and Program Study Statistics -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-tie mr-1"></i>
                    Distribusi Berdasarkan Jabatan
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $positionStats = [];
                    foreach ($lecturersData as $lecturer) {
                        $position = $lecturer['position'] ?? 'Tidak Diketahui';
                        if (!isset($positionStats[$position])) {
                            $positionStats[$position] = 0;
                        }
                        $positionStats[$position]++;
                    }

                    $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                    $colorIndex = 0;
                    ?>
                    <?php foreach ($positionStats as $position => $count): ?>
                        <div class="col-12 mb-2">
                            <div class="progress-group">
                                <span class="float-right"><b><?= $count ?>/<?= count($lecturersData) ?></b></span>
                                <span class="progress-text"><?= esc($position) ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-<?= $colors[$colorIndex % count($colors)] ?>"
                                        style="width: <?= count($lecturersData) > 0 ? round(($count / count($lecturersData)) * 100, 1) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php $colorIndex++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    Distribusi Program Studi
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $programStats = [];
                    foreach ($lecturersData as $lecturer) {
                        $program = $lecturer['study_program'] ?? 'Tidak Diketahui';
                        $programLabel = match ($program) {
                            'bisnis_digital' => 'Bisnis Digital',
                            'informatika' => 'Informatika',
                            'sistem_informasi' => 'Sistem Informasi',
                            'sains_data' => 'Sains Data',
                            'magister_teknologi_informasi' => 'Magister TI',
                            default => $program
                        };

                        if (!isset($programStats[$programLabel])) {
                            $programStats[$programLabel] = 0;
                        }
                        $programStats[$programLabel]++;
                    }

                    $colorIndex = 0;
                    ?>
                    <?php foreach ($programStats as $program => $count): ?>
                        <div class="col-12 mb-2">
                            <div class="progress-group">
                                <span class="float-right"><b><?= $count ?>/<?= count($lecturersData) ?></b></span>
                                <span class="progress-text"><?= esc($program) ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-<?= $colors[$colorIndex % count($colors)] ?>"
                                        style="width: <?= count($lecturersData) > 0 ? round(($count / count($lecturersData)) * 100, 1) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <?php $colorIndex++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>