<?php
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Aktivitas Terbaru</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Tanggal</th>
                                <th style="width: 20%">Pengguna</th>
                                <th>Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activities ?? [])): ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada aktivitas terbaru</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td><?= $activity['date'] ?? '-' ?></td>
                                        <td><?= $activity['user'] ?? '-' ?></td>
                                        <td><?= $activity['activity'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>