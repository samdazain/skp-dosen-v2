<div class="row mt-4">
    <!-- Daily Absence Stats -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-times mr-1"></i>
                    Absen Harian
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary"><?= number_format($statistics['average_daily_score'] ?? 0, 1) ?></h3>
                    <p class="mb-0">Rata-rata Skor</p>
                </div>
                <?php if (isset($statistics['daily'])): ?>
                    <div class="progress-group mt-3">
                        <span class="progress-text">Tidak Ada Alpha (0)</span>
                        <span class="float-right">
                            <b><?= $statistics['daily']['count_no_alpha'] ?? 0 ?></b>/<?= $statistics['daily']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $statistics['daily']['percentage_no_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">1 - 2 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['daily']['count_1_2_alpha'] ?? 0 ?></b>/<?= $statistics['daily']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?= $statistics['daily']['percentage_1_2_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">3 - 4 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['daily']['count_3_4_alpha'] ?? 0 ?></b>/<?= $statistics['daily']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?= $statistics['daily']['percentage_3_4_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">≥ 5 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['daily']['count_above_5_alpha'] ?? 0 ?></b>/<?= $statistics['daily']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: <?= $statistics['daily']['percentage_above_5_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Data statistik tidak tersedia</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Exercise Absence Stats -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-running mr-1"></i>
                    Absen Senam
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary"><?= number_format($statistics['average_exercise_score'] ?? 0, 1) ?></h3>
                    <p class="mb-0">Rata-rata Skor</p>
                </div>
                <?php if (isset($statistics['exercise'])): ?>
                    <div class="progress-group mt-3">
                        <span class="progress-text">Tidak Ada Alpha (0)</span>
                        <span class="float-right">
                            <b><?= $statistics['exercise']['count_no_alpha'] ?? 0 ?></b>/<?= $statistics['exercise']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $statistics['exercise']['percentage_no_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">1 - 2 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['exercise']['count_1_2_alpha'] ?? 0 ?></b>/<?= $statistics['exercise']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?= $statistics['exercise']['percentage_1_2_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">3 - 4 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['exercise']['count_3_4_alpha'] ?? 0 ?></b>/<?= $statistics['exercise']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?= $statistics['exercise']['percentage_3_4_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">≥ 5 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['exercise']['count_above_5_alpha'] ?? 0 ?></b>/<?= $statistics['exercise']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: <?= $statistics['exercise']['percentage_above_5_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Data statistik tidak tersedia</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ceremony Absence Stats -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-flag mr-1"></i>
                    Absen Upacara
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary"><?= number_format($statistics['average_ceremony_score'] ?? 0, 1) ?></h3>
                    <p class="mb-0">Rata-rata Skor</p>
                </div>
                <?php if (isset($statistics['ceremony'])): ?>
                    <div class="progress-group mt-3">
                        <span class="progress-text">Tidak Ada Alpha (0)</span>
                        <span class="float-right">
                            <b><?= $statistics['ceremony']['count_no_alpha'] ?? 0 ?></b>/<?= $statistics['ceremony']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?= $statistics['ceremony']['percentage_no_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">1 - 2 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['ceremony']['count_1_2_alpha'] ?? 0 ?></b>/<?= $statistics['ceremony']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: <?= $statistics['ceremony']['percentage_1_2_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">3 - 4 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['ceremony']['count_3_4_alpha'] ?? 0 ?></b>/<?= $statistics['ceremony']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: <?= $statistics['ceremony']['percentage_3_4_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">≥ 5 Alpha</span>
                        <span class="float-right">
                            <b><?= $statistics['ceremony']['count_above_5_alpha'] ?? 0 ?></b>/<?= $statistics['ceremony']['total'] ?? 0 ?> dosen
                        </span>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: <?= $statistics['ceremony']['percentage_above_5_alpha'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Data statistik tidak tersedia</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>