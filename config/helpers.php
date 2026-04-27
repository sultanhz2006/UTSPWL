<?php

$sessionLifetime = 86400;

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

function base_url(string $path = ''): string
{
    $base = '/UTSPWL';
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $route): void
{
    header('Location: index.php?route=' . $route);
    exit;
}

function is_logged_in(): bool
{
    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        return false;
    }

    $expiresAt = $_SESSION['user']['expires_at'] ?? 0;
    if ($expiresAt < time()) {
        unset($_SESSION['user']);
        set_flash('error', 'Sesi login sudah habis. Silakan masuk lagi.');
        return false;
    }

    return true;
}

function user_role(): string
{
    return $_SESSION['user']['role'] ?? 'user';
}

function is_admin(): bool
{
    return user_role() === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Silakan masuk terlebih dahulu.');
        redirect('login');
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        set_flash('error', 'Akses ini hanya untuk admin.');
        redirect('books.index');
    }
}

function guest_only(): void
{
    if (is_logged_in()) {
        redirect('books.index');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function get_flash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);

    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Token CSRF tidak valid.');
    }
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function remembered_username(): string
{
    return $_COOKIE['remember_username'] ?? '';
}

function store_old_input(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old_input(): void
{
    unset($_SESSION['old']);
}

function selected(string $current, string $value): string
{
    return $current === $value ? 'selected' : '';
}

function render(string $view, array $data = []): void
{
    extract($data);
    $viewFile = __DIR__ . '/../views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        http_response_code(404);
        exit('View tidak ditemukan.');
    }

    include __DIR__ . '/../views/layouts/header.php';
    include $viewFile;
    include __DIR__ . '/../views/layouts/footer.php';
}

function upload_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['gambar' => null, 'thumbpath' => null];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Gagal mengunggah gambar sampul.');
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 2MB.');
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($extension, $allowed, true)) {
        throw new RuntimeException('Format gambar hanya boleh JPG atau PNG.');
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new RuntimeException('File yang diunggah bukan gambar yang valid.');
    }

    $source = create_image_resource($file['tmp_name'], $imageInfo['mime']);
    if (!$source) {
        throw new RuntimeException('GD Library tidak bisa memproses gambar ini.');
    }

    $name = uniqid('cover_', true) . '.jpg';
    $thumbName = uniqid('thumb_', true) . '.jpg';
    $uploadDir = __DIR__ . '/../uploads/';
    $thumbDir = $uploadDir . 'thumbs/';
    $target = $uploadDir . $name;
    $thumbTarget = $thumbDir . $thumbName;

    resize_and_save_image($source, (int) $imageInfo[0], (int) $imageInfo[1], 900, 1200, $target);
    resize_and_save_image($source, (int) $imageInfo[0], (int) $imageInfo[1], 240, 320, $thumbTarget);
    imagedestroy($source);

    return [
        'gambar' => 'uploads/' . $name,
        'thumbpath' => 'uploads/thumbs/' . $thumbName,
    ];
}

function create_image_resource(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($path),
        'image/png' => imagecreatefrompng($path),
        default => null,
    };
}

function resize_and_save_image($source, int $sourceWidth, int $sourceHeight, int $maxWidth, int $maxHeight, string $destination): void
{
    $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight, 1);
    $newWidth = (int) round($sourceWidth * $ratio);
    $newHeight = (int) round($sourceHeight * $ratio);

    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    $background = imagecolorallocate($canvas, 18, 18, 18);
    imagefill($canvas, 0, 0, $background);

    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    imagejpeg($canvas, $destination, 82);
    imagedestroy($canvas);
}

function delete_image_files(?string $gambar, ?string $thumbpath): void
{
    foreach ([$gambar, $thumbpath] as $path) {
        if (!$path) {
            continue;
        }

        $fullPath = __DIR__ . '/../' . ltrim($path, '/');
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
