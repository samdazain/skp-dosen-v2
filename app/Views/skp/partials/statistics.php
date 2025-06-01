<div class="row mt-4">
    <!-- Summary Cards -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $statistics['category_distribution']['sangat_baik'] ?></h3>
                <p>Sangat Baik (≥88)</p>
            </div>
            <div class="icon">
                <i class="fas fa-trophy"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $statistics['category_distribution']['baik'] ?></h3>
                <p>Baik (76-87)</p>
            </div>
            <div class="icon">
                <i class="fas fa-thumbs-up"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $statistics['category_distribution']['cukup'] ?></h3>
                <p>Cukup (61-75)</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-paper"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $statistics['category_distribution']['kurang'] ?></h3>
                <p>Kurang (&lt;61) </p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <!-- Component Averages Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Rata-rata Komponen Penilaian
                </h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="componentChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- SKP Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi Kategori SKP
                </h3>
            </div>
            <div class="card-body">
                <!-- Average SKP Score -->
                <div class="info-box bg-gradient-info mb-3">
                    <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rata-rata Nilai SKP</span>
                        <span class="info-box-number"><?= number_format($statistics['average_skp_score'], 1) ?></span>
                    </div>
                </div>

                <!-- Category Distribution -->
                <?php
                $totalLecturers = $statistics['total_lecturers'];
                $distribution = $statistics['category_distribution'];
                ?>

                <div class="progress-group">
                    <span class="progress-text">Sangat Baik</span>
                    <span class="float-right"><b><?= $distribution['sangat_baik'] ?></b>/<?= $totalLecturers ?>
                        dosen</span>
                    <div class="progress">
                        <div class="progress-bar bg-success"
                            style="width: <?= $totalLecturers > 0 ? round(($distribution['sangat_baik'] / $totalLecturers) * 100, 1) : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Baik</span>
                    <span class="float-right"><b><?= $distribution['baik'] ?></b>/<?= $totalLecturers ?> dosen</span>
                    <div class="progress">
                        <div class="progress-bar bg-primary"
                            style="width: <?= $totalLecturers > 0 ? round(($distribution['baik'] / $totalLecturers) * 100, 1) : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Cukup</span>
                    <span class="float-right"><b><?= $distribution['cukup'] ?></b>/<?= $totalLecturers ?> dosen</span>
                    <div class="progress">
                        <div class="progress-bar bg-warning"
                            style="width: <?= $totalLecturers > 0 ? round(($distribution['cukup'] / $totalLecturers) * 100, 1) : 0 ?>%">
                        </div>
                    </div>
                </div>

                <div class="progress-group">
                    <span class="progress-text">Kurang</span>
                    <span class="float-right"><b><?= $distribution['kurang'] ?></b>/<?= $totalLecturers ?> dosen</span>
                    <div class="progress">
                        <div class="progress-bar bg-danger"
                            style="width: <?= $totalLecturers > 0 ? round(($distribution['kurang'] / $totalLecturers) * 100, 1) : 0 ?>%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Component Statistics Details -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Detail Statistik Komponen
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-shield-alt text-primary mr-1"></i> Integritas</td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($statistics['component_averages']['integrity'], 1) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $integrityAvg = $statistics['component_averages']['integrity'];
                                        if ($integrityAvg >= 88) {
                                            echo '<span class="badge badge-success">Sangat Baik</span>';
                                        } elseif ($integrityAvg >= 76) {
                                            echo '<span class="badge badge-primary">Baik</span>';
                                        } elseif ($integrityAvg >= 61) {
                                            echo '<span class="badge badge-warning">Cukup</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Kurang</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-clock text-info mr-1"></i> Disiplin</td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($statistics['component_averages']['discipline'], 1) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $disciplineAvg = $statistics['component_averages']['discipline'];
                                        if ($disciplineAvg >= 88) {
                                            echo '<span class="badge badge-success">Sangat Baik</span>';
                                        } elseif ($disciplineAvg >= 76) {
                                            echo '<span class="badge badge-primary">Baik</span>';
                                        } elseif ($disciplineAvg >= 61) {
                                            echo '<span class="badge badge-warning">Cukup</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Kurang</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-handshake text-warning mr-1"></i> Komitmen</td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($statistics['component_averages']['commitment'], 1) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $commitmentAvg = $statistics['component_averages']['commitment'];
                                        if ($commitmentAvg >= 88) {
                                            echo '<span class="badge badge-success">Sangat Baik</span>';
                                        } elseif ($commitmentAvg >= 76) {
                                            echo '<span class="badge badge-primary">Baik</span>';
                                        } elseif ($commitmentAvg >= 61) {
                                            echo '<span class="badge badge-warning">Cukup</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Kurang</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-users text-success mr-1"></i> Kerjasama</td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($statistics['component_averages']['cooperation'], 1) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $cooperationAvg = $statistics['component_averages']['cooperation'];
                                        if ($cooperationAvg >= 88) {
                                            echo '<span class="badge badge-success">Sangat Baik</span>';
                                        } elseif ($cooperationAvg >= 76) {
                                            echo '<span class="badge badge-primary">Baik</span>';
                                        } elseif ($cooperationAvg >= 61) {
                                            echo '<span class="badge badge-warning">Cukup</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Kurang</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-heart text-danger mr-1"></i> Orientasi Pelayanan</td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($statistics['component_averages']['orientation'], 1) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $orientationAvg = $statistics['component_averages']['orientation'];
                                        if ($orientationAvg >= 88) {
                                            echo '<span class="badge badge-success">Sangat Baik</span>';
                                        } elseif ($orientationAvg >= 76) {
                                            echo '<span class="badge badge-primary">Baik</span>';
                                        } elseif ($orientationAvg >= 61) {
                                            echo '<span class="badge badge-warning">Cukup</span>';
                                        } else {
                                            echo '<span class="badge badge-danger">Kurang</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center bg-light">
                                        <strong>
                                            <i class="fas fa-calculator text-primary mr-1"></i>
                                            Total Dosen: <?= $statistics['total_lecturers'] ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Component Averages Chart
        var componentCtx = document.getElementById('componentChart').getContext('2d');
        var componentChart = new Chart(componentCtx, {
            type: 'bar',
            data: {
                labels: ['Integritas', 'Disiplin', 'Komitmen', 'Kerjasama', 'Orientasi'],
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: [
                        <?= $statistics['component_averages']['integrity'] ?>,
                        <?= $statistics['component_averages']['discipline'] ?>,
                        <?= $statistics['component_averages']['commitment'] ?>,
                        <?= $statistics['component_averages']['cooperation'] ?>,
                        <?= $statistics['component_averages']['orientation'] ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + ' pts';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(1) + ' pts';
                            }
                        }
                    }
                }
            }
        });

        // Add some animation effects
        $('.small-box').hover(
            function() {
                $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
            },
            function() {
                $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
            }
        );
    });
</script>

<style>
    .small-box {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .progress-group {
        margin-bottom: 1rem;
    }

    .progress-text {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .component-score {
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .info-box-number {
        font-size: 2rem;
        font-weight: bold;
    }

    .chart {
        height: 250px;
    }
</style>