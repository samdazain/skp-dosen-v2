<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Filter Data
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('cooperation') ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="position">Jabatan</label>
                        <select name="position" id="position" class="form-control">
                            <option value="">Semua Jabatan</option>
                            <option value="DEKAN" <?= ($filters['position'] ?? '') === 'DEKAN' ? 'selected' : '' ?>>
                                Dekan</option>
                            <option value="WAKIL DEKAN I"
                                <?= ($filters['position'] ?? '') === 'WAKIL DEKAN I' ? 'selected' : '' ?>>Wakil Dekan I
                            </option>
                            <option value="WAKIL DEKAN II"
                                <?= ($filters['position'] ?? '') === 'WAKIL DEKAN II' ? 'selected' : '' ?>>Wakil Dekan
                                II</option>
                            <option value="WAKIL DEKAN III"
                                <?= ($filters['position'] ?? '') === 'WAKIL DEKAN III' ? 'selected' : '' ?>>Wakil Dekan
                                III</option>
                            <option value="Dosen Prodi"
                                <?= ($filters['position'] ?? '') === 'Dosen Prodi' ? 'selected' : '' ?>>Dosen Prodi
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="study_program">Program Studi</label>
                        <select name="study_program" id="study_program" class="form-control">
                            <option value="">Semua Program Studi</option>
                            <option value="informatika"
                                <?= ($filters['study_program'] ?? '') === 'informatika' ? 'selected' : '' ?>>Informatika
                            </option>
                            <option value="sistem_informasi"
                                <?= ($filters['study_program'] ?? '') === 'sistem_informasi' ? 'selected' : '' ?>>Sistem
                                Informasi</option>
                            <option value="sains_data"
                                <?= ($filters['study_program'] ?? '') === 'sains_data' ? 'selected' : '' ?>>Sains Data
                            </option>
                            <option value="bisnis_digital"
                                <?= ($filters['study_program'] ?? '') === 'bisnis_digital' ? 'selected' : '' ?>>Bisnis
                                Digital</option>
                            <option value="magister_teknologi_informasi"
                                <?= ($filters['study_program'] ?? '') === 'magister_teknologi_informasi' ? 'selected' : '' ?>>
                                Magister TI</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="level">Tingkat Kerja Sama</label>
                        <select name="level" id="level" class="form-control">
                            <option value="">Semua Tingkat</option>
                            <option value="very_cooperative"
                                <?= ($filters['level'] ?? '') === 'very_cooperative' ? 'selected' : '' ?>>Sangat
                                Kooperatif</option>
                            <option value="cooperative"
                                <?= ($filters['level'] ?? '') === 'cooperative' ? 'selected' : '' ?>>Kooperatif</option>
                            <option value="fair" <?= ($filters['level'] ?? '') === 'fair' ? 'selected' : '' ?>>Cukup
                                Kooperatif</option>
                            <option value="not_cooperative"
                                <?= ($filters['level'] ?? '') === 'not_cooperative' ? 'selected' : '' ?>>Tidak
                                Kooperatif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group d-block">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('cooperation') ?>" class="btn btn-secondary">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>