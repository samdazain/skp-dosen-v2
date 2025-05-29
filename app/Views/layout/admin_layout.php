<?php

/**
 * @var CodeIgniter\View\View $this
 */

helper('navigation');
?>
<!DOCTYPE html>
<html>

<head>
    <?= view('layout/partials/head') ?>
    <script>
        const baseUrl = '<?= base_url() ?>';
    </script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/semester_selector.css') ?>">
    <?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/alerts.css') ?>">
    <?= $this->renderSection('styles') ?>
    <?= $this->endSection() ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        <?= view('layout/partials/navbar') ?>

        <!-- Mobile Backdrop -->
        <div class="sidebar-backdrop"></div>

        <!-- Sidebar -->
        <?= view('layout/partials/sidebar') ?>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?= view('layout/partials/footer') ?>
    </div>

    <!-- Logout Confirmation Modal -->
    <?= view('layout/partials/logout_modal') ?>

    <!-- SweetAlert2 JS - Load before custom scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin_layout.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>