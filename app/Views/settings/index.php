<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pengaturan Akun</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                <ul class="mb-0">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- User Information Card -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div
                                class="profile-user-img img-fluid img-circle d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-user fa-3x text-secondary"></i>
                            </div>
                        </div>

                        <h3 class="profile-username text-center"><?= $user['name'] ?></h3>

                        <p class="text-muted text-center">
                            <?php
                            $roleLabels = [
                                'admin' => 'Administrator',
                                'dekan' => 'Dekan',
                                'wadek1' => 'Wakil Dekan 1',
                                'wadek2' => 'Wakil Dekan 2',
                                'wadek3' => 'Wakil Dekan 3',
                                'kaprodi' => 'Ketua Program Studi',
                                'staff' => 'Staff'
                            ];
                            echo $roleLabels[$user['role']] ?? ucfirst($user['role']);
                            ?>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <!-- <b>Email</b> <a class="float-right"><?= $user['email'] ?></a> -->
                                <b>Email</b> <a class="float-right"><?= 'admin@gmail.com' ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Role</b>
                                <span class="float-right">
                                    <?php
                                    $badgeClasses = [
                                        'admin' => 'badge-danger',
                                        'dekan' => 'badge-primary',
                                        'wadek1' => 'badge-info',
                                        'wadek2' => 'badge-info',
                                        'wadek3' => 'badge-info',
                                        'kaprodi' => 'badge-success',
                                        'staff' => 'badge-secondary'
                                    ];
                                    $badgeClass = $badgeClasses[$user['role']] ?? 'badge-secondary';
                                    echo '<span class="badge ' . $badgeClass . '">' . ucfirst($user['role']) . '</span>';
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Password</h3>
                    </div>

                    <form action="<?= base_url('settings/change-password') ?>" method="post">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" placeholder="Masukkan password saat ini" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="new_password">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        placeholder="Masukkan password baru" required minlength="6">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="new_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Password minimal 6 karakter</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password Baru <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" placeholder="Masukkan kembali password baru" required
                                        minlength="6">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="confirm_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Pastikan sama dengan password baru</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="icon fas fa-info-circle"></i> Tips keamanan:
                                <ul class="mb-0">
                                    <li>Gunakan kombinasi huruf besar, kecil, angka, dan simbol</li>
                                    <li>Jangan gunakan password yang mudah ditebak</li>
                                    <li>Jangan menggunakan password yang sama dengan akun lain</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                // Toggle input type between password and text
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>