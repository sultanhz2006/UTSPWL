<?php

class AuthController
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function showLogin(): void
    {
        guest_only();
        render('auth/login', [
            'title' => 'Masuk',
            'bodyClass' => 'auth-page',
        ]);
    }

    public function showRegister(): void
    {
        guest_only();
        render('auth/register', [
            'title' => 'Registrasi',
            'bodyClass' => 'auth-page',
        ]);
    }

    public function login(): void
    {
        guest_only();
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberUsername = isset($_POST['remember_username']);
        $email = trim($_POST['email'] ?? '');

        store_old_input(['username' => $username]);

        if ($username === '' || $password === '') {
            set_flash('error', 'Username dan password wajib diisi.');
            redirect('login');
        }

        if ($email !== '' && strpos($email, '@') === false) {
            set_flash('error', 'Format email tidak valid.');
            redirect('login');
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('error', 'Login Gagal!');
            redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'username' => $user['username'],
            'role' => $user['role'] ?? 'user',
            'expires_at' => time() + 86400,
        ];

        if ($rememberUsername) {
            setcookie('remember_username', $user['username'], time() + 86400, '/', '', false, true);
        } else {
            setcookie('remember_username', '', time() - 3600, '/');
        }

        clear_old_input();
        set_flash('success', 'Selamat datang, ' . $user['username'] . '!');
        redirect('books.index');
    }

    public function register(): void
    {
        guest_only();
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $email = trim($_POST['email'] ?? '');

        store_old_input(['username' => $username, 'email' => $email]);

        if ($username === '' || $password === '' || $passwordConfirmation === '' || $email === '') {
            set_flash('error', 'Semua field registrasi wajib diisi.');
            redirect('register');
        }

        if (strpos($email, '@') === false) {
            set_flash('error', 'Isian email tidak valid.');
            redirect('register');
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            set_flash('error', 'Username hanya boleh huruf, angka, atau underscore dengan panjang 3-20 karakter.');
            redirect('register');
        }

        if ($password !== $passwordConfirmation) {
            set_flash('error', 'Konfirmasi password tidak cocok.');
            redirect('register');
        }

        if (strlen($password) < 3) {
            set_flash('error', 'Password minimal 3 karakter.');
            redirect('register');
        }

        if ($this->userModel->existsByUsername($username)) {
            set_flash('error', 'Username sudah digunakan.');
            redirect('register');
        }

        $this->userModel->create($username, password_hash($password, PASSWORD_DEFAULT), $email);
        clear_old_input();
        set_flash('success', 'Registrasi berhasil. Silakan masuk.');
        redirect('login');
    }

    public function logout(): void
    {
        require_login();
        verify_csrf();

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        session_start();
        set_flash('success', 'Anda berhasil keluar.');
        redirect('login');
    }
}
