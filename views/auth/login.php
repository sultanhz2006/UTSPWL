<div class="floating-orb orb-one"></div>
<div class="floating-orb orb-two"></div>

<div class="auth-wrapper d-flex align-items-center justify-content-center">
    <div class="auth-shell w-100 p-4 p-lg-5">
        <div class="text-center mb-4">
            <span class="badge rounded-pill text-bg-info-subtle border border-info-subtle text-info-emphasis px-3 py-2 mb-3">Sistem Login</span>
            <h1 class="fw-bold mb-2">Masuk ke Inventaris Perpustakaan</h1>
            <p class="text-secondary mb-0">Gunakan akun yang tersimpan di database untuk mengelola data buku.</p>
        </div>

        <form method="POST" action="index.php?route=login.submit" class="vstack gap-3">
            <?= csrf_input() ?>
            <div>
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="username"
                    name="username"
                    value="<?= e(old('username', remembered_username())) ?>"
                    placeholder="Masukkan username"
                    required
                >
            </div>
            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control form-control-lg"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>
            <div class="form-check text-secondary">
                <input
                    class="form-check-input"
                    type="checkbox"
                    value="1"
                    id="remember_username"
                    name="remember_username"
                    <?= remembered_username() !== '' ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="remember_username">Ingat username saya</label>
            </div>
            <button type="submit" class="btn btn-info btn-lg btn-soft fw-semibold">Masuk</button>
        </form>

        <div class="mt-4 pt-3 border-top border-secondary-subtle text-secondary small text-center">
            Belum ada akun? <a href="index.php?route=register" class="link-info link-underline-opacity-0 link-underline-opacity-100-hover">Registrasi</a>
        </div>
    </div>
</div>
