<?php

use Config\Navigation;

$navConfig = new Navigation();
$menuItems = $navConfig->getSidebarMenu();
?>

<!-- Link to external sidebar CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css') ?>">

<aside class="main-sidebar elevation-4 modern-sidebar">
    <!-- Brand Logo -->
    <div class="brand-link modern-brand">
        <div class="brand-logo-container">
            <img src="<?= base_url('assets/logo_skp.png') ?>" alt="SKP Dosen Logo" class="brand-image">
            <div class="brand-overlay"></div>
        </div>
        <span class="brand-text">
            <span class="brand-main">SKP</span>
            <span class="brand-sub">Dosen</span>
        </span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar modern-sidebar-content">
        <!-- User Info Panel -->
        <div class="user-panel gap-1 mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
            <div class="info">
                <a href="#" class="d-block user-name">
                    <?= session()->get('user_name') ?? 'Admin' ?>
                </a>
                <small class="user-role">
                    <i class="fas fa-circle status-indicator"></i>
                    <?= ucfirst(session()->get('user_role') ?? 'Administrator') ?>
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column modern-nav" data-widget="treeview" role="menu"
                data-accordion="false">
                <?php foreach ($menuItems as $item): ?>
                    <li class="nav-item modern-nav-item">
                        <a href="<?= $item['path'] ?>" class="nav-link modern-nav-link <?= $item['active'] ?>"
                            data-has-access="<?= $item['hasAccess'] ? 'true' : 'false' ?>">
                            <div class="nav-icon-container">
                                <i class="nav-icon <?= $item['icon'] ?>"></i>
                                <div class="icon-background"></div>
                            </div>
                            <span class="nav-text">
                                <?= $item['label'] ?>
                            </span>
                            <div class="nav-indicator"></div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>