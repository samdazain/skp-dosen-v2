<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-1"></i>
                    Filter Data
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="position_filter">Jabatan</label>
                                <select class="form-control" id="position_filter" name="position">
                                    <option value="">Semua Jabatan</option>
                                    <option value="DEKAN"
                                        <?= (request()->getGet('position') === 'DEKAN') ? 'selected' : '' ?>>DEKAN
                                    </option>
                                    <option value="WAKIL DEKAN I"
                                        <?= (request()->getGet('position') === 'WAKIL DEKAN I') ? 'selected' : '' ?>>
                                        WAKIL DEKAN I</option>
                                    <option value="WAKIL DEKAN II"
                                        <?= (request()->getGet('position') === 'WAKIL DEKAN II') ? 'selected' : '' ?>>
                                        WAKIL DEKAN II</option>
                                    <option value="WAKIL DEKAN III"
                                        <?= (request()->getGet('position') === 'WAKIL DEKAN III') ? 'selected' : '' ?>>
                                        WAKIL DEKAN III</option>
                                    <option value="KOORPRODI IF"
                                        <?= (request()->getGet('position') === 'KOORPRODI IF') ? 'selected' : '' ?>>
                                        KOORPRODI IF</option>
                                    <option value="KOORPRODI SI"
                                        <?= (request()->getGet('position') === 'KOORPRODI SI') ? 'selected' : '' ?>>
                                        KOORPRODI SI</option>
                                    <option value="KOORPRODI SD"
                                        <?= (request()->getGet('position') === 'KOORPRODI SD') ? 'selected' : '' ?>>
                                        KOORPRODI SD</option>
                                    <option value="KOORPRODI BD"
                                        <?= (request()->getGet('position') === 'KOORPRODI BD') ? 'selected' : '' ?>>
                                        KOORPRODI BD</option>
                                    <option value="KOORPRODI MTI"
                                        <?= (request()->getGet('position') === 'KOORPRODI MTI') ? 'selected' : '' ?>>
                                        KOORPRODI MTI</option>
                                    <option value="Ka Lab SCR"
                                        <?= (request()->getGet('position') === 'Ka Lab SCR') ? 'selected' : '' ?>>
                                        Ka Lab SCR</option>
                                    <option value="Ka Lab PPSTI"
                                        <?= (request()->getGet('position') === 'Ka Lab PPSTI') ? 'selected' : '' ?>>
                                        Ka Lab PPSTI</option>
                                    <option value="Ka Lab SOLUSI"
                                        <?= (request()->getGet('position') === 'Ka Lab SOLUSI') ? 'selected' : '' ?>>
                                        Ka Lab SOLUSI</option>
                                    <option value="Ka Lab MSI"
                                        <?= (request()->getGet('position') === 'Ka Lab MSI') ? 'selected' : '' ?>>
                                        Ka Lab MSI</option>
                                    <option value="Ka Lab Sains Data"
                                        <?= (request()->getGet('position') === 'Ka Lab Sains Data') ? 'selected' : '' ?>>
                                        Ka Lab Sains Data</option>
                                    <option value="Ka Lab BISDI"
                                        <?= (request()->getGet('position') === 'Ka Lab BISDI') ? 'selected' : '' ?>>
                                        Ka Lab BISDI</option>
                                    <option value="Ka Lab MTI"
                                        <?= (request()->getGet('position') === 'Ka Lab MTI') ? 'selected' : '' ?>>
                                        Ka Lab MTI</option>
                                    <option value="Ka UPT TIK"
                                        <?= (request()->getGet('position') === 'Ka UPT TIK') ? 'selected' : '' ?>>
                                        Ka UPT TIK</option>
                                    <option value="Ka UPA PKK"
                                        <?= (request()->getGet('position') === 'Ka UPA PKK') ? 'selected' : '' ?>>
                                        Ka UPA PKK</option>
                                    <option value="Ka Pengembangan Pembelajaran LPMPP"
                                        <?= (request()->getGet('position') === 'Ka Pengembangan Pembelajaran LPMPP') ? 'selected' : '' ?>>
                                        Ka Pengembangan Pembelajaran LPMPP</option>
                                    <option value="PPMB"
                                        <?= (request()->getGet('position') === 'PPMB') ? 'selected' : '' ?>>
                                        PPMB</option>
                                    <option value="KOORDINATOR PUSAT KARIR DAN TRACER STUDY"
                                        <?= (request()->getGet('position') === 'KOORDINATOR PUSAT KARIR DAN TRACER STUDY') ? 'selected' : '' ?>>
                                        KOORDINATOR PUSAT KARIR DAN TRACER STUDY</option>
                                    <option value="LSP UPNVJT"
                                        <?= (request()->getGet('position') === 'LSP UPNVJT') ? 'selected' : '' ?>>
                                        LSP UPNVJT</option>
                                    <option value="UPT TIK"
                                        <?= (request()->getGet('position') === 'UPT TIK') ? 'selected' : '' ?>>
                                        UPT TIK</option>
                                    <option value="Dosen Prodi"
                                        <?= (request()->getGet('position') === 'Dosen Prodi') ? 'selected' : '' ?>>
                                        Dosen Prodi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="program_filter">Program Studi</label>
                                <select class="form-control" id="program_filter" name="study_program">
                                    <option value="">Semua Program Studi</option>
                                    <option value="bisnis_digital"
                                        <?= (request()->getGet('study_program') === 'bisnis_digital') ? 'selected' : '' ?>>
                                        Bisnis Digital</option>
                                    <option value="informatika"
                                        <?= (request()->getGet('study_program') === 'informatika') ? 'selected' : '' ?>>
                                        Informatika</option>
                                    <option value="sistem_informasi"
                                        <?= (request()->getGet('study_program') === 'sistem_informasi') ? 'selected' : '' ?>>
                                        Sistem Informasi</option>
                                    <option value="sains_data"
                                        <?= (request()->getGet('study_program') === 'sains_data') ? 'selected' : '' ?>>
                                        Sains Data</option>
                                    <option value="magister_teknologi_informasi"
                                        <?= (request()->getGet('study_program') === 'magister_teknologi_informasi') ? 'selected' : '' ?>>
                                        Magister Teknologi Informasi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="competence_filter">Status Kompetensi</label>
                                <select class="form-control" id="competence_filter" name="competence">
                                    <option value="">Semua Status</option>
                                    <option value="active"
                                        <?= (request()->getGet('competence') === 'active') ? 'selected' : '' ?>>Aktif
                                    </option>
                                    <option value="inactive"
                                        <?= (request()->getGet('competence') === 'inactive') ? 'selected' : '' ?>>Tidak
                                        Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tridharma_filter">Status Tri Dharma</label>
                                <select class="form-control" id="tridharma_filter" name="tridharma">
                                    <option value="">Semua Status</option>
                                    <option value="1" <?= (request()->getGet('tridharma') === '1') ? 'selected' : '' ?>>
                                        Lulus</option>
                                    <option value="0" <?= (request()->getGet('tridharma') === '0') ? 'selected' : '' ?>>
                                        Tidak Lulus</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= current_url() ?>" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                            <div class="float-right">
                                <span class="text-muted">
                                    Menampilkan <?= count($lecturersData) ?> dari <?= $stats['total_lecturers'] ?> dosen
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>