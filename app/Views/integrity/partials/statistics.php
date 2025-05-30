<?php

/**
 * Integrity Statistics Partial
 * 
 * @var array $statistics
 */

$stats = $statistics ?? [
    'average_attendance_score' => 0,
    'average_courses_score' => 0,
    'total_lecturers' => 0,
    'score_distribution' => [
        'excellent' => 0,
        'good' => 0,
        'fair' => 0,
        'poor' => 0
    ]
];
?>

<div class="row mt-4">
    <!-- Main Statistics -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Statistik Integritas Dosen
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon">
                                <i class="fas fa-user-check"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Rata-rata Skor Kehadiran</span>
                                <span class="info-box-number">
                                    <?= number_format($stats['average_attendance_score'], 1) ?>
                                </span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $stats['average_attendance_score'] ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Rata-rata Skor Beban Mengajar</span>
                                <span class="info-box-number">
                                    <?= number_format($stats['average_courses_score'], 1) ?>
                                </span>
                                <div class="progress">
                                    <div class="progress-bar bg-success"
                                        style="width: <?= $stats['average_courses_score'] ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="progress-group">
                            <span class="progress-text">Rata-rata Skor Integritas Keseluruhan</span>
                            <span class="float-right">
                                <b><?= number_format(($stats['average_attendance_score'] + $stats['average_courses_score']) / 2, 1) ?></b>/100
                            </span>
                            <div class="progress">
                                <div class="progress-bar bg-primary"
                                    style="width: <?= ($stats['average_attendance_score'] + $stats['average_courses_score']) / 2 ?>%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Distribution -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Distribusi Skor
                </h3>
            </div>
            <div class="card-body">
                <div class="progress-group">
                    <span class="progress-text">Sangat Baik (88)</span>
                    <span class="float-right">
                        <b><?= $stats['score_distribution']['excellent'] ?></b>/<?= $stats['total_lecturers'] ?> dosen
                    </span>
                    <div class="progress">
                        <div class="progress-bar bg-success"
                            style="width: <?= $stats['total_lecturers'] > 0 ? ($stats['score_distribution']['excellent'] / $stats['total_lecturers']) * 100 : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Baik (75-87)</span>
                    <span class="float-right">
                        <b><?= $stats['score_distribution']['good'] ?></b>/<?= $stats['total_lecturers'] ?> dosen
                    </span>
                    <div class="progress">
                        <div class="progress-bar bg-primary"
                            style="width: <?= $stats['total_lecturers'] > 0 ? ($stats['score_distribution']['good'] / $stats['total_lecturers']) * 100 : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Cukup (60-74)</span>
                    <span class="float-right">
                        <b><?= $stats['score_distribution']['fair'] ?></b>/<?= $stats['total_lecturers'] ?> dosen
                    </span>
                    <div class="progress">
                        <div class="progress-bar bg-warning"
                            style="width: <?= $stats['total_lecturers'] > 0 ? ($stats['score_distribution']['fair'] / $stats['total_lecturers']) * 100 : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Kurang (&lt;60)</span>
                    <span class="float-right">
                        <b><?= $stats['score_distribution']['poor'] ?></b>/<?= $stats['total_lecturers'] ?>
                        dosen
                    </span>
                    <div class="progress">
                        <div class="progress-bar bg-danger"
                            style="width: <?= $stats['total_lecturers'] > 0 ? ($stats['score_distribution']['poor'] / $stats['total_lecturers']) * 100 : 0 ?>%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>