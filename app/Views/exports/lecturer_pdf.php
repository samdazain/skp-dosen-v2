<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen Fakultas Ilmu Komputer</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F4E79;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #1F4E79;
            margin: 0 0 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 20px 0;
        }

        .table th {
            background-color: #2F5597;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #2F5597;
            font-size: 9px;
        }

        .table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            vertical-align: middle;
            font-size: 8px;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>UNIVERSITAS PEMBANGUNAN NASIONAL "VETERAN" JAWA TIMUR</h1>
        <h2>FAKULTAS ILMU KOMPUTER</h2>
        <h3><?= esc($title) ?></h3>
        <p><?= esc($dateInfo) ?></p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIP</th>
                <th style="width: 30%;">Nama Dosen</th>
                <th style="width: 30%;">Jabatan</th>
                <th style="width: 20%;">Program Studi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($lecturers as $lecturer): ?>
                <tr>
                    <td style="text-align: center;"><?= $no ?></td>
                    <td style="text-align: center;"><?= esc($lecturer['nip']) ?></td>
                    <td><?= esc($lecturer['name']) ?></td>
                    <td><?= esc($lecturer['position']) ?></td>
                    <td style="text-align: center;"><?= esc($lecturer['study_program'] ?? '-') ?></td>
                </tr>
                <?php $no++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <div style="text-align: center; font-weight: bold; margin-bottom: 30px; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd;">
            Total Data Dosen: <?= number_format($totalRecords) ?> orang
        </div>

        <div style="text-align: right; margin-top: 40px;">
            <div style="margin-bottom: 60px;">Surabaya, <?= date('d F Y') ?></div>
            <div style="font-weight: bold;">Dekan Fakultas Ilmu Komputer</div>
        </div>
    </div>
</body>

</html>