<?php

helper('navigation')
?>

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
                <?= get_role_badge(session()->get('user_role')) ?>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>