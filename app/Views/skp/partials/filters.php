<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Filter Data
        </h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('skp') ?>" id="filterForm">
            <div class="row">
                <div class="col-md-4">
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
                                <option value="<?= $value ?>" <?= (isset($filters['position']) && $filters['position'] === $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
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
                                <option value="<?= $value ?>" <?= (isset($filters['study_program']) && $filters['study_program'] === $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="skp_category">Kategori SKP</label>
                        <select name="skp_category" id="skp_category" class="form-control select2">
                            <option value="">Semua Kategori</option>
                            <option value="Sangat Baik" <?= (isset($filters['skp_category']) && $filters['skp_category'] === 'Sangat Baik') ? 'selected' : '' ?>>
                                Sangat Baik (≥88)
                            </option>
                            <option value="Baik" <?= (isset($filters['skp_category']) && $filters['skp_category'] === 'Baik') ? 'selected' : '' ?>>
                                Baik (76-87)
                            </option>
                            <option value="Cukup" <?= (isset($filters['skp_category']) && $filters['skp_category'] === 'Cukup') ? 'selected' : '' ?>>
                                Cukup (61-75)
                            </option>
                            <option value="Kurang" <?= (isset($filters['skp_category']) && $filters['skp_category'] === 'Kurang') ? 'selected' : '' ?>>
                                Kurang (<61)
                                    </option>
                            <option value="Belum Dinilai" <?= (isset($filters['skp_category']) && $filters['skp_category'] === 'Belum Dinilai') ? 'selected' : '' ?>>
                                Belum Dinilai
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
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
                            if (!empty($filters['skp_category'])) {
                                $activeFilters[] = 'Kategori: ' . $filters['skp_category'];
                            }
                            echo implode(', ', $activeFilters);
                            ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-right">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Terapkan Filter
                        </button>
                        <?php if (!empty(array_filter($filters ?? []))): ?>
                            <a href="<?= base_url('skp') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        <?php endif; ?>
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
        $('#position, #study_program, #skp_category').on('change', function() {
            $('#filterForm').submit();
        });
    });
</script>