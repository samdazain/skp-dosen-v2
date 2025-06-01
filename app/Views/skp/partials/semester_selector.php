<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-1"></i>
            Informasi Semester & Navigasi
        </h3>
        <div class="card-tools">
            <!-- Use the component semester selector -->
            <?= view('components/semester_selector', [
                'semesters' => $allSemesters ?? [],
                'activeSemester' => $currentSemester
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="info-box bg-gradient-info">
                    <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Semester Aktif SKP</span>
                        <span class="info-box-number">
                            <?php if ($currentSemester): ?>
                                <?= $currentSemester['year'] ?>/<?= $currentSemester['term'] === '1' ? 'Ganjil' : 'Genap' ?>
                            <?php else: ?>
                                Belum Ada
                            <?php endif; ?>
                        </span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            Data SKP untuk semester ini
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-gradient-success">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Dosen</span>
                        <span class="info-box-number">
                            <?= isset($statistics['total_lecturers']) ? $statistics['total_lecturers'] : 0 ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional info row -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-info"></i> Informasi SKP</h5>
                    Data SKP menampilkan rata-rata nilai dari 5 komponen: Integritas, Disiplin, Komitmen, Kerjasama, dan
                    Orientasi Pelayanan.
                    Gunakan dropdown di atas untuk beralih antar semester.
                </div>
            </div>
        </div>
    </div>
</div>