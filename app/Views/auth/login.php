<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Login | SKP Dosen</title>
    <meta name="description" content="Login page for SKP Dosen Fasilkom" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="/favicon.ico" />

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>

<body>
    <!-- Animated Background -->
    <div class="background-animation">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
            <div class="shape shape-6"></div>
        </div>
        <div class="gradient-overlay"></div>
    </div>

    <!-- Main Login Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Header Section -->
            <div class="login-header">
                <div class="logo-container">
                    <img class="logo" src="<?= base_url('assets/logo.png') ?>" alt="Logo" />
                    <div class="logo-glow"></div>
                </div>
                <h1 class="title">SKP Dosen</h1>
                <p class="subtitle">Fakultas Ilmu Komputer</p>
                <div class="header-line"></div>
            </div>

            <!-- Alert Messages -->
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= session('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->has('message')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= session('message') ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="login-form">
                <?= form_open(base_url('login'), ['autocomplete' => 'on', 'class' => 'form']) ?>

                <div class="input-group">
                    <div class="input-container">
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" class="input-field" name="nip" id="nip" placeholder="Masukkan NIP Anda"
                            value="<?= old('nip') ?>" autocomplete="username" required />
                        <label class="input-label">NIP</label>
                        <div class="input-border"></div>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-container">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" class="input-field" name="password" id="password"
                            placeholder="Masukkan Password Anda" autocomplete="current-password" required />
                        <label class="input-label">Password</label>
                        <div class="input-border"></div>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Masuk</span>
                    <div class="btn-loader">
                        <div class="spinner"></div>
                    </div>
                    <i class="fas fa-arrow-right btn-icon"></i>
                </button>

                <?= form_close() ?>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p class="footer-text">
                    <i class="fas fa-shield-alt"></i>
                    Sistem Penilaian Kinerja Dosen
                </p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="decoration decoration-left"></div>
        <div class="decoration decoration-right"></div>
    </div>

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/login.js') ?>"></script>

    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const passwordEye = document.getElementById('password-eye');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordEye.classList.remove('fa-eye');
                passwordEye.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordEye.classList.remove('fa-eye-slash');
                passwordEye.classList.add('fa-eye');
            }
        }

        // Form submission animation
        document.querySelector('.form').addEventListener('submit', function(e) {
            const btn = document.querySelector('.login-btn');
            btn.classList.add('loading');
        });

        // Input focus animations
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });

            // Check if input has value on page load
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
    </script>
</body>

</html>