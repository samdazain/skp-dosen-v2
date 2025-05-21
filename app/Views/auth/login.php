<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login | SKP Dosen</title>
    <meta name="description" content="Login page for SKP Dosen Fasilkom" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="/favicon.ico" />

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI for autocomplete -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">

</head>

<body>
    <div class="login-container">
        <img class="logo" src="<?= base_url('assets/logo.png') ?>" alt="Logo" />
        <h1 class="title">SKP Dosen Fasilkom</h1>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post" autocomplete="on">
            <div class="input-container">
                <div class="input-icon">
                    <div class="icon-user"></div>
                </div>
                <input type="text" class="input-field" name="nip" id="nip" placeholder="NIP" autocomplete="username"
                    required />
            </div>

            <div class="input-container">
                <div class="input-icon">
                    <div class="icon-lock"></div>
                </div>
                <input type="password" class="input-field" name="password" placeholder="Password"
                    autocomplete="current-password" required />
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="credentials-info">
            <p><strong>Demo Credentials:</strong></p>
            <p>NIP: 123456789 | Password: password</p>
            <p>NIP: admin | Password: password</p>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>

</html>