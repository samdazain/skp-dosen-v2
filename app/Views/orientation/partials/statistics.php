<div class="row mt-4">
    <div class="col-lg-6">
        <!-- Score Range Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Panduan Penilaian Orientasi Pelayanan
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Rentang Nilai Angket</th>
                            <th class="text-center">Nilai Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>3.1 - 4.0</td>
                            <td class="text-center text-success font-weight-bold">88</td>
                            <td class="text-center"><span class="badge badge-success">Sangat Baik</span></td>
                        </tr>
                        <tr>
                            <td>2.6 - 3.0</td>
                            <td class="text-center text-primary font-weight-bold">80</td>
                            <td class="text-center"><span class="badge badge-primary">Baik</span></td>
                        </tr>
                        <tr>
                            <td>2.1 - 2.5</td>
                            <td class="text-center text-warning font-weight-bold">70</td>
                            <td class="text-center"><span class="badge badge-warning">Cukup</span></td>
                        </tr>
                        <tr>
                            <td>1.0 - 2.0</td>
                            <td class="text-center text-danger font-weight-bold">60</td>
                            <td class="text-center"><span class="badge badge-danger">Kurang</span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Nilai angket dalam rentang 1.0 - 4.0, dihitung berdasarkan pengaturan skor dalam database
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Statistik Orientasi Pelayanan
                </h3>
            </div>
            <div class="card-body">
                <!-- Average Scores -->
                <div class="row">
                    <div class="col-6">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Rata-rata Nilai Angket</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    <?= isset($statistics['average_questionnaire_score']) ? number_format($statistics['average_questionnaire_score'], 2) : '0.00' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text text-center text-muted">Rata-rata Nilai Total</span>
                                <span class="info-box-number text-center text-muted mb-0">
                                    <?= isset($statistics['average_score']) ? number_format($statistics['average_score'], 1) : '0.0' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div class="mt-3">
                    <h6 class="text-center">Distribusi Status</h6>
                    <?php
                    $totalLecturers = $statistics['total_lecturers'] ?? 0;
                    $distribution = $statistics['score_distribution'] ?? ['excellent' => 0, 'good' => 0, 'fair' => 0, 'poor' => 0];
                    ?>

                    <div class="progress-group">
                        <span class="progress-text">Sangat Baik</span>
                        <span class="float-right"><b><?= $distribution['excellent'] ?></b>/<?= $totalLecturers ?> dosen</span>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $totalLecturers > 0 ? round(($distribution['excellent'] / $totalLecturers) * 100, 1) : 0 ?>%"></div>
                        </div>
                    </div>

                    <div class="progress-group">
                        <span class="progress-text">Baik</span>
                        <span class="float-right"><b><?= $distribution['good'] ?></b>/<?= $totalLecturers ?> dosen</span>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?= $totalLecturers > 0 ? round(($distribution['good'] / $totalLecturers) * 100, 1) : 0 ?>%"></div>
                        </div>
                    </div>

                    <div class="progress-group">
                        <span class="progress-text">Cukup</span>
                        <span class="float-right"><b><?= $distribution['fair'] ?></b>/<?= $totalLecturers ?> dosen</span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?= $totalLecturers > 0 ? round(($distribution['fair'] / $totalLecturers) * 100, 1) : 0 ?>%"></div>
                        </div>
                    </div>

                    <div class="progress-group">
                        <span class="progress-text">Kurang</span>
                        <span class="float-right"><b><?= $distribution['poor'] ?></b>/<?= $totalLecturers ?> dosen</span>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: <?= $totalLecturers > 0 ? round(($distribution['poor'] / $totalLecturers) * 100, 1) : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>