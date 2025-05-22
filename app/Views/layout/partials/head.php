<?php

/**
 * @var CodeIgniter\View\View $this
 */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $pageTitle ?? 'Admin Panel' ?></title>

<!-- AdminLTE CSS -->
<link rel="stylesheet" href="/adminlte/css/adminlte.min.css">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="/adminlte-plugins/fontawesome-free/css/all.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- Custom CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/admin_layout.css') ?>">

<!-- jQuery -->
<script src="/adminlte-plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/adminlte-plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('styles') ?>