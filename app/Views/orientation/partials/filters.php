<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Filter Data
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('orientation') ?>" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="position">Jabatan</label>
                        <select name="position" id="position" class="form-control select2">
                            <option value="">Semua Jabatan</option>
                            <?php
                            $positions = [
                                'DEKAN' => 'Dekan',
                                'WAKIL DEKAN I' => 'Wakil Dekan I',
                                'WAKIL DEKAN II' => 'Wakil Dekan II',
                                'WAKIL DEKAN III' => 'Wakil Dekan III',
                                'KOORPRODI IF' => 'Koordinator Prodi IF',
                                'KOORPRODI SI' => 'Koordinator Prodi SI',
                                'KOORPRODI SD' => 'Koordinator Prodi SD',
                                'KOORPRODI BD' => 'Koordinator Prodi BD',
                                'KOORPRODI MTI' => 'Koordinator Prodi MTI',
                                'Ka Lab SCR' => 'Ka Lab SCR',
                                'Ka Lab PPSTI' => 'Ka Lab PPSTI',
                                'Ka Lab SOLUSI' => 'Ka Lab SOLUSI',
                                'Ka Lab MSI' => 'Ka Lab MSI',
                                'Ka Lab Sains Data' => 'Ka Lab Sains Data',
                                'Ka Lab BISDI' => 'Ka Lab BISDI',
                                'Ka Lab MTI' => 'Ka Lab MTI',
                                'Ka UPT TIK' => 'Ka UPT TIK',
                                'Ka UPA PKK' => 'Ka UPA PKK',
                                'Ka Pengembangan Pembelajaran LPMPP' => 'Ka Pengembangan Pembelajaran LPMPP',
                                'PPMB' => 'PPMB',
                                'KOORDINATOR PUSAT KARIR DAN TRACER STUDY' => 'Koordinator Pusat Karir',
                                'LSP UPNVJT' => 'LSP UPNVJT',
                                'UPT TIK' => 'UPT TIK',
                                'Dosen Prodi' => 'Dosen Prodi'
                            ];
                            foreach ($positions as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= (isset($filters['position']) && $filters['position'] === $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="study_program">Program Studi</label>
                        <select name="study_program" id="study_program" class="form-control select2">
                            <option value="">Semua Program Studi</option>
                            <?php
                            $programs = [
                                'bisnis_digital' => 'Bisnis Digital',
                                'informatika' => 'Informatika',
                                'sistem_informasi' => 'Sistem Informasi',
                                'sains_data' => 'Sains Data',
                                'magister_teknologi_informasi' => 'Magister TI'
                            ];
                            foreach ($programs as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= (isset($filters['study_program']) && $filters['study_program'] === $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="score_range">Rentang Nilai</label>
                        <select name="score_range" id="score_range" class="form-control select2">
                            <option value="">Semua Nilai</option>
                            <option value="excellent"
                                <?= (isset($filters['score_range']) && $filters['score_range'] === 'excellent') ? 'selected' : '' ?>>
                                Sangat Baik (≥88)
                            </option>
                            <option value="good"
                                <?= (isset($filters['score_range']) && $filters['score_range'] === 'good') ? 'selected' : '' ?>>
                                Baik (80-87)
                            </option>
                            <option value="fair"
                                <?= (isset($filters['score_range']) && $filters['score_range'] === 'fair') ? 'selected' : '' ?>>
                                Cukup (70-79)
                            </option>
                            <option value="poor"
                                <?= (isset($filters['score_range']) && $filters['score_range'] === 'poor') ? 'selected' : '' ?>>
                                Kurang (<70) </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-block">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <?php if (!empty(array_filter($filters ?? []))): ?>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Filter aktif:
                                    <?php
                                    $activeFilters = [];
                                    if (!empty($filters['position'])) {
                                        $activeFilters[] = 'Jabatan: ' . ($positions[$filters['position']] ?? $filters['position']);
                                    }
                                    if (!empty($filters['study_program'])) {
                                        $activeFilters[] = 'Program Studi: ' . ($programs[$filters['study_program']] ?? $filters['study_program']);
                                    }
                                    if (!empty($filters['score_range'])) {
                                        $scoreLabels = [
                                            'excellent' => 'Sangat Baik (≥88)',
                                            'good' => 'Baik (80-87)',
                                            'fair' => 'Cukup (70-79)',
                                            'poor' => 'Kurang (<70)'
                                        ];
                                        $activeFilters[] = 'Nilai: ' . ($scoreLabels[$filters['score_range']] ?? $filters['score_range']);
                                    }
                                    echo implode(', ', $activeFilters);
                                    ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!empty(array_filter($filters ?? []))): ?>
                                <a href="<?= base_url('orientation') ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times mr-1"></i> Reset Filter
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Auto-submit on filter change
        $('#position, #study_program, #score_range').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>