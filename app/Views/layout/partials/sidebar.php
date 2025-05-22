<?php

use Config\Navigation;

$navConfig = new Navigation();
$menuItems = $navConfig->getSidebarMenu();
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/dashboard" class="brand-link">
        <img src="<?= base_url('assets/logo_skp.png') ?>" alt="SKP Dosen Logo">
        <span class="brand-text font-weight-light">SKP Dosen</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <?php foreach ($menuItems as $item): ?>
                    <li class="nav-item">
                        <a href="<?= $item['path'] ?>" class="nav-link <?= $item['active'] ?>">
                            <i class="nav-icon <?= $item['icon'] ?>"></i>
                            <p><?= $item['label'] ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>