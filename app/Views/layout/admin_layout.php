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

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin_layout.js') ?>"></script>


    <?= $this->renderSection('scripts') ?>
</body>

</html>