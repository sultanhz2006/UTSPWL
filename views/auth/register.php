<div class="floating-orb orb-one"></div>
<div class="floating-orb orb-two"></div>

<div class="auth-wrapper d-flex align-items-center justify-content-center">
    <div class="auth-shell w-100 p-4 p-lg-5">
        <div class="text-center mb-4">
            <span class="badge rounded-pill text-bg-info-subtle border border-info-subtle text-info-emphasis px-3 py-2 mb-3">Registrasi</span>
            <h1 class="fw-bold mb-2">Buat Akun Baru</h1>
            <p class="text-secondary mb-0">Daftarkan username dan password untuk mulai mengelola inventaris perpustakaan.</p>
        </div>

        <form method="POST" action="index.php?route=register.submit" class="vstack gap-3">
            <?= csrf_input() ?>
            <div>
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="username"
                    name="username"
                    value="<?= e(old('username')) ?>"
                    placeholder="Minimal 3 karakter"
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
                    placeholder="Buat password"
                    required
                >
            </div>
            <div>
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input
                    type="password"
                    class="form-control form-control-lg"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi password"
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
                    value="<?= e(old('email')) ?>"
                    placeholder="Masukkan email"
                    required
                    oninput="validateEmail(this)"
                >
                <div id="email-error" class="text-danger small mt-1" style="display:none;">Isian tidak valid</div>
            </div>
            <button type="submit" class="btn btn-info btn-lg btn-soft fw-semibold">Registrasi</button>
        </form>

        <div class="mt-4 pt-3 border-top border-secondary-subtle text-secondary small text-center">
            Sudah punya akun? <a href="index.php?route=login" class="link-info link-underline-opacity-0 link-underline-opacity-100-hover">Masuk</a>
        </div>

        <div class="mt-3 pt-3 border-top border-secondary-subtle text-center">
            <span class="text-secondary small">A12.2024.07163 &nbsp;·&nbsp; Muhammad Sultan Hafidz Herawan</span>
        </div>
    </div>
</div>

<script>
function validateEmail(input) {
    var errorEl = document.getElementById('email-error');
    if (input.value.indexOf('@') === -1) {
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
    } else {
        errorEl.style.display = 'none';
        input.classList.remove('is-invalid');
    }
}
</script>
