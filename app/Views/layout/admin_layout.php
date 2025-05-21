<!DOCTYPE html>
<html>

<head>
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
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <!-- User Info -->
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="nav-link">
                        <i class="fas fa-user-circle mr-1"></i>
                        <strong><?= session()->get('user_name') ?></strong>
                        <?php
                        $roleLabels = [
                            'admin' => '<span class="badge badge-danger ml-1">Admin</span>',
                            'dekan' => '<span class="badge badge-primary ml-1">Dekan</span>',
                            'wadek1' => '<span class="badge badge-info ml-1">Wadek 1</span>',
                            'wadek2' => '<span class="badge badge-info ml-1">Wadek 2</span>',
                            'wadek3' => '<span class="badge badge-info ml-1">Wadek 3</span>',
                            'kaprodi' => '<span class="badge badge-success ml-1">Kaprodi</span>',
                            'staff' => '<span class="badge badge-secondary ml-1">Staff</span>'
                        ];
                        echo $roleLabels[session()->get('user_role')] ?? '<span class="badge badge-secondary ml-1">' . ucfirst(session()->get('user_role')) . '</span>';
                        ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Mobile Backdrop -->
        <div class="sidebar-backdrop"></div>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/dashboard" class="brand-link">
                <img src="<?= base_url('assets/logo_skp.png') ?>" alt="SKP Dosen Logo">
                <span class="brand-text font-weight-light">SKP Dosen</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item"><a href="/dashboard" class="nav-link"><i
                                    class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="/skp" class="nav-link"><i class="nav-icon fas fa-file-alt"></i>
                                <p>SKP Master</p>
                            </a></li>
                        <li class="nav-item"><a href="/lecturers" class="nav-link"><i
                                    class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>Daftar Dosen</p>
                            </a></li>
                        <li class="nav-item"><a href="/integrity" class="nav-link"><i
                                    class="nav-icon fas fa-shield-alt"></i>
                                <p>Data Integritas</p>
                            </a></li>
                        <li class="nav-item"><a href="/discipline" class="nav-link"><i
                                    class="nav-icon fas fa-tasks"></i>
                                <p>Data Disiplin</p>
                            </a></li>
                        <li class="nav-item"><a href="/commitment" class="nav-link"><i
                                    class="nav-icon fas fa-handshake"></i>
                                <p>Komitmen</p>
                            </a></li>
                        <li class="nav-item"><a href="/cooperation" class="nav-link"><i
                                    class="nav-icon fas fa-users"></i>
                                <p>Kerja Sama</p>
                            </a></li>
                        <li class="nav-item"><a href="/orientation" class="nav-link"><i
                                    class="nav-icon fas fa-concierge-bell"></i>
                                <p>Orientasi Pelayanan</p>
                            </a></li>
                        <li class="nav-item"><a href="/score" class="nav-link"><i class="nav-icon fas fa-edit"></i>
                                <p>Setting Nilai</p>
                            </a></li>
                        <li class="nav-item"><a href="/archive" class="nav-link"><i class="nav-icon fas fa-archive"></i>
                                <p>Arsip File</p>
                            </a></li>
                        <li class="nav-item"><a href="/history" class="nav-link"><i class="nav-icon fas fa-history"></i>
                                <p>Riwayat Aktivitas</p>
                            </a></li>
                        <li class="nav-item"><a href="/user" class="nav-link"><i class="nav-icon fas fa-user-cog"></i>
                                <p>Manajemen User</p>
                            </a></li>
                        <li class="nav-item"><a href="/settings" class="nav-link"><i class="nav-icon fas fa-cog"></i>
                                <p>Pengaturan Akun</p>
                            </a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>&copy; <?= date('Y') ?> SKP Dosen.</strong> All rights reserved.
        </footer>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-confirm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="icon-box">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h4 class="modal-title">Konfirmasi Logout</h4>
                    <p class="mb-4">Apakah Anda yakin ingin keluar dari sistem?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="<?= base_url('logout') ?>" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/sidebar.js') ?>"></script>

    <script>
        $(document).ready(function() {
            // Show logout modal
            $('#logoutBtn').click(function() {
                $('#logoutModal').modal('show');
            });
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>