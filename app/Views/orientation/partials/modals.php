<!-- Information Modal (for readonly data explanation) -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Informasi Data Orientasi Pelayanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle mr-1"></i> Tentang Data Orientasi Pelayanan</h6>
                    <p class="mb-2">Data orientasi pelayanan dosen menampilkan nilai berdasarkan angket pengajaran yang telah dikumpulkan melalui sistem terpisah.</p>
                </div>

                <h6>Karakteristik Data:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-lock text-warning mr-2"></i> <strong>Readonly:</strong> Data tidak dapat diubah melalui interface ini</li>
                    <li><i class="fas fa-sync-alt text-info mr-2"></i> <strong>Auto-sync:</strong> Data disinkronkan otomatis dari sistem angket</li>
                    <li><i class="fas fa-calculator text-success mr-2"></i> <strong>Auto-calculate:</strong> Skor dihitung otomatis berdasarkan nilai angket</li>
                </ul>

                <h6>Rentang Penilaian:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Nilai Angket</th>
                                <th class="text-center">Skor</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>4.1 - 5.0</td>
                                <td class="text-center"><span class="badge badge-success">88</span></td>
                                <td class="text-center">Sangat Baik</td>
                            </tr>
                            <tr>
                                <td>3.1 - 4.0</td>
                                <td class="text-center"><span class="badge badge-primary">80</span></td>
                                <td class="text-center">Baik</td>
                            </tr>
                            <tr>
                                <td>2.1 - 3.0</td>
                                <td class="text-center"><span class="badge badge-warning">70</span></td>
                                <td class="text-center">Cukup</td>
                            </tr>
                            <tr>
                                <td>1.0 - 2.0</td>
                                <td class="text-center"><span class="badge badge-danger">60</span></td>
                                <td class="text-center">Kurang</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Options Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Opsi Export Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Pilih format export yang diinginkan:</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                <h6>Excel Format</h6>
                                <p class="text-muted small">Data dalam format spreadsheet untuk analisis lebih lanjut</p>
                                <a href="<?= base_url('orientation/export-excel') . '?' . http_build_query(request()->getGet()) ?>"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-download mr-1"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                <h6>PDF Format</h6>
                                <p class="text-muted small">Data dalam format PDF untuk dokumentasi dan laporan</p>
                                <a href="<?= base_url('orientation/export-pdf') . '?' . http_build_query(request()->getGet()) ?>"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-download mr-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>