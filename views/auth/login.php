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
            <div>
                <label for="email" class="form-label">Email</label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="email"
                    name="email"
                    value="112202407163@mhs.dinus.ac.id"
                    placeholder="Masukkan email"
                    oninput="validateLoginEmail(this)"
                >
                <div id="login-email-error" class="text-danger small mt-1" style="display:none;">Isian tidak valid</div>
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

        <div class="mt-3 pt-3 border-top border-secondary-subtle text-center">
            <span class="text-secondary small">A12.2024.07163 &nbsp;·&nbsp; Muhammad Sultan Hafidz Herawan</span>
        </div>
    </div>
</div>

<script>
function validateLoginEmail(input) {
    var errorEl = document.getElementById('login-email-error');
    if (input.value.length > 0 && input.value.indexOf('@') === -1) {
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorEl.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}
</script>
