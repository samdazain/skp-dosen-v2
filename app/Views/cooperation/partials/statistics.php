<div class="row mt-4">
    <!-- Cooperation Level Distribution -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Distribusi Tingkat Kerja Sama
                </h3>
            </div>
            <div class="card-body">
                <?php
                // Get level counts and percentages
                $levelCounts = $stats['level_counts'] ?? [];
                $levelPercentages = $stats['level_percentages'] ?? [];
                $totalLecturers = $stats['total_lecturers'] ?? 0;

                // Define level display data
                $levelData = [
                    'very_cooperative' => [
                        'label' => 'Sangat Kooperatif',
                        'class' => 'bg-success',
                        'count' => $levelCounts['very_cooperative'] ?? 0,
                        'percentage' => $levelPercentages['very_cooperative'] ?? 0
                    ],
                    'cooperative' => [
                        'label' => 'Kooperatif',
                        'class' => 'bg-primary',
                        'count' => $levelCounts['cooperative'] ?? 0,
                        'percentage' => $levelPercentages['cooperative'] ?? 0
                    ],
                    'fair' => [
                        'label' => 'Cukup Kooperatif',
                        'class' => 'bg-info',
                        'count' => $levelCounts['fair'] ?? 0,
                        'percentage' => $levelPercentages['fair'] ?? 0
                    ],
                    'not_cooperative' => [
                        'label' => 'Tidak Kooperatif',
                        'class' => 'bg-danger',
                        'count' => $levelCounts['not_cooperative'] ?? 0,
                        'percentage' => $levelPercentages['not_cooperative'] ?? 0
                    ]
                ];
                ?>

                <?php foreach ($levelData as $level => $data): ?>
                    <div class="progress-group">
                        <span class="progress-text"><?= $data['label'] ?></span>
                        <span class="float-right">
                            <b><?= $data['count'] ?></b>/<?= $totalLecturers ?> dosen
                            (<?= number_format($data['percentage'], 1) ?>%)
                        </span>
                        <div class="progress">
                            <div class="progress-bar <?= $data['class'] ?>"
                                style="width: <?= $data['percentage'] ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($totalLecturers == 0): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>Belum ada data untuk ditampilkan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ringkasan Statistik
                </h3>
            </div>
            <div class="card-body">
                <?php
                // Calculate average score
                $totalScore = 0;
                $countWithScore = 0;
                $highestScore = 0;
                $lowestScore = 100;

                foreach ($lecturersData as $lecturer) {
                    $score = $lecturer['score'] ?: 0;
                    if ($score == 0) {
                        // Calculate score based on level
                        $levelValues = [
                            'very_cooperative' => 90,
                            'cooperative' => 80,
                            'fair' => 70,
                            'not_cooperative' => 60
                        ];
                        $score = $levelValues[$lecturer['level']] ?? 60;
                    }

                    $totalScore += $score;
                    $countWithScore++;

                    if ($score > $highestScore) $highestScore = $score;
                    if ($score < $lowestScore) $lowestScore = $score;
                }

                $averageScore = $countWithScore > 0 ? round($totalScore / $countWithScore, 1) : 0;
                if ($countWithScore == 0) {
                    $highestScore = 0;
                    $lowestScore = 0;
                }
                ?>

                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">Total Dosen</span>
                        <span class="info-box-number text-center text-muted mb-0"><?= $totalLecturers ?></span>
                    </div>
                </div>

                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">Rata-rata Nilai</span>
                        <span class="info-box-number text-center text-muted mb-0"><?= $averageScore ?></span>
                    </div>
                </div>

                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">Nilai Tertinggi</span>
                        <span class="info-box-number text-center text-muted mb-0"><?= $highestScore ?></span>
                    </div>
                </div>

                <div class="info-box bg-light">
                    <div class="info-box-content">
                        <span class="info-box-text text-center text-muted">Nilai Terendah</span>
                        <span class="info-box-number text-center text-muted mb-0"><?= $lowestScore ?></span>
                    </div>
                </div>

                <!-- Quick stats -->
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Kooperatif & Sangat Kooperatif:</strong>
                        <?= ($levelCounts['cooperative'] ?? 0) + ($levelCounts['very_cooperative'] ?? 0) ?> dosen
                        (<?= number_format((($levelPercentages['cooperative'] ?? 0) + ($levelPercentages['very_cooperative'] ?? 0)), 1) ?>%)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Level Distribution Chart -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi Kerja Sama per Tingkat
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($levelData as $level => $data): ?>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon <?= str_replace('bg-', 'bg-gradient-', $data['class']) ?>">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?= $data['label'] ?></span>
                                    <span class="info-box-number">
                                        <?= $data['count'] ?>
                                        <small>(<?= number_format($data['percentage'], 1) ?>%)</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalLecturers > 0): ?>
                    <!-- Performance Indicators -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Indikator Kinerja Kerja Sama</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    $excellentRate = ($levelPercentages['very_cooperative'] ?? 0);
                                    $excellentClass = $excellentRate >= 50 ? 'text-success' : ($excellentRate >= 25 ? 'text-warning' : 'text-danger');
                                    ?>
                                    <div class="small-box bg-gradient-success">
                                        <div class="inner">
                                            <h3 class="<?= $excellentClass ?>"><?= number_format($excellentRate, 1) ?>%</h3>
                                            <p>Sangat Kooperatif</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <?php
                                    $goodRate = ($levelPercentages['cooperative'] ?? 0) + ($levelPercentages['very_cooperative'] ?? 0);
                                    $goodClass = $goodRate >= 75 ? 'text-success' : ($goodRate >= 50 ? 'text-warning' : 'text-danger');
                                    ?>
                                    <div class="small-box bg-gradient-primary">
                                        <div class="inner">
                                            <h3 class="<?= $goodClass ?>"><?= number_format($goodRate, 1) ?>%</h3>
                                            <p>Kooperatif & Sangat Kooperatif</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <?php
                                    $needsImprovement = ($levelPercentages['not_cooperative'] ?? 0);
                                    $improvementClass = $needsImprovement <= 10 ? 'text-success' : ($needsImprovement <= 25 ? 'text-warning' : 'text-danger');
                                    ?>
                                    <div class="small-box bg-gradient-warning">
                                        <div class="inner">
                                            <h3 class="<?= $improvementClass ?>"><?= number_format($needsImprovement, 1) ?>%</h3>
                                            <p>Perlu Perbaikan</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>