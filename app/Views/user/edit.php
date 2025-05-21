<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('user') ?>">Manajemen Pengguna</a></li>
                    <li class="breadcrumb-item active">Edit Pengguna</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Pengguna</h3>
                    </div>

                    <form action="<?= base_url('user/update/' . $userData['id']) ?>" method="post">
                        <div class="card-body">
                            <?php if (session()->has('errors')): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach (session('errors') as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan nama lengkap" value="<?= old('name') ?? $userData['name'] ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="nip">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nip" name="nip"
                                    placeholder="Masukkan NIP (18 digit)" value="<?= old('nip') ?? $userData['nip'] ?>"
                                    required minlength="18" maxlength="18" pattern="\d{18}"
                                    title="NIP harus terdiri dari 18 digit angka">
                                <small class="form-text text-muted">Format: 18 digit angka</small>
                            </div>

                            <div class="form-group">
                                <label for="position">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position" name="position"
                                    placeholder="Masukkan jabatan"
                                    value="<?= old('position') ?? $userData['position'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Masukkan alamat email"
                                    value="<?= old('email') ?? $userData['email'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin"
                                        <?= (old('role') ?? $userData['role']) == 'admin' ? 'selected' : '' ?>>Admin
                                    </option>
                                    <option value="dekan"
                                        <?= (old('role') ?? $userData['role']) == 'dekan' ? 'selected' : '' ?>>Dekan
                                    </option>
                                    <option value="wadek1"
                                        <?= (old('role') ?? $userData['role']) == 'wadek1' ? 'selected' : '' ?>>Wakil
                                        Dekan 1</option>
                                    <option value="wadek2"
                                        <?= (old('role') ?? $userData['role']) == 'wadek2' ? 'selected' : '' ?>>Wakil
                                        Dekan 2</option>
                                    <option value="wadek3"
                                        <?= (old('role') ?? $userData['role']) == 'wadek3' ? 'selected' : '' ?>>Wakil
                                        Dekan 3</option>
                                    <option value="kaprodi"
                                        <?= (old('role') ?? $userData['role']) == 'kaprodi' ? 'selected' : '' ?>>Kaprodi
                                    </option>
                                    <option value="staff"
                                        <?= (old('role') ?? $userData['role']) == 'staff' ? 'selected' : '' ?>>Staff
                                    </option>
                                </select>
                            </div>

                            <div class="form-group" id="study-program-group"
                                style="display: <?= (old('role') ?? $userData['role']) == 'kaprodi' ? 'block' : 'none' ?>;">
                                <label for="study_program">Program Studi <span class="text-danger">*</span></label>
                                <select class="form-control" id="study_program" name="study_program">
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach ($studyPrograms as $program): ?>
                                        <option value="<?= $program ?>"
                                            <?= (old('study_program') ?? $userData['study_program']) == $program ? 'selected' : '' ?>>
                                            <?= $program ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Wajib diisi jika role adalah Kaprodi</small>
                            </div>

                            <hr>
                            <h5>Ubah Password</h5>
                            <p class="text-muted">Kosongkan jika tidak ingin mengubah password</p>

                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password baru" minlength="6">
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirm"
                                    name="password_confirm" placeholder="Masukkan kembali password baru">
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="<?= base_url('user') ?>" class="btn btn-default">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Show/hide study program field based on role selection
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const studyProgramGroup = document.getElementById('study-program-group');
        const studyProgramSelect = document.getElementById('study_program');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'kaprodi') {
                studyProgramGroup.style.display = 'block';
                studyProgramSelect.setAttribute('required', 'required');
            } else {
                studyProgramGroup.style.display = 'none';
                studyProgramSelect.removeAttribute('required');
                if (this.value !== 'kaprodi') {
                    studyProgramSelect.value = '';
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>