<?php
$pageTitle = $title ?? 'Aplikasi Inventaris Perpustakaan';
$bodyClassName = $bodyClass ?? '';
$flashSuccess = get_flash('success');
$flashError = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #090b11;
            --bg-panel: #111522;
            --bg-card: #171c2c;
            --border-soft: rgba(255, 255, 255, 0.08);
            --text-main: #f5f7fb;
            --text-soft: #aeb7cd;
            --accent: #3fb3ff;
            --accent-2: #6ee7b7;
            --danger: #ff6b81;
            --warning: #ffd166;
            --shadow-soft: 0 18px 40px rgba(0, 0, 0, 0.35);
        }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(63, 179, 255, 0.18), transparent 28%),
                radial-gradient(circle at bottom left, rgba(110, 231, 183, 0.12), transparent 24%),
                linear-gradient(160deg, #07090f 0%, #0d1220 45%, #080b12 100%);
            color: var(--text-main);
        }

        .topbar-shell,
        .card-dark,
        .stat-card,
        .table-dark-shell,
        .auth-shell,
        .form-shell {
            background: linear-gradient(180deg, rgba(23, 28, 44, 0.92), rgba(14, 18, 30, 0.94));
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-soft);
        }

        .topbar-shell {
            border-radius: 1.25rem;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(16px);
        }

        .hero-panel {
            padding: 2rem;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(63, 179, 255, 0.18), rgba(110, 231, 183, 0.08));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow-soft);
        }

        .stat-card,
        .card-dark,
        .form-shell,
        .auth-shell,
        .table-dark-shell {
            border-radius: 1.25rem;
        }

        .stat-card,
        .book-card {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeSlideUp 0.55s ease forwards;
        }

        .stat-card:nth-child(2),
        .book-card:nth-child(2) {
            animation-delay: 0.08s;
        }

        .stat-card:nth-child(3),
        .book-card:nth-child(3) {
            animation-delay: 0.16s;
        }

        .table-dark {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            --bs-table-border-color: rgba(255, 255, 255, 0.08);
            margin-bottom: 0;
        }

        .table-dark td,
        .table-dark th {
            padding-top: 1rem;
            padding-bottom: 1rem;
            vertical-align: middle;
        }

        .btn-soft {
            border-radius: 999px;
            padding: 0.72rem 1.2rem;
            transition: transform 0.22s ease, box-shadow 0.22s ease, opacity 0.22s ease;
        }

        .btn-soft:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.28);
        }

        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: rgba(63, 179, 255, 0.7);
            box-shadow: 0 0 0 0.25rem rgba(63, 179, 255, 0.15);
            color: var(--text-main);
        }

        .form-control::placeholder {
            color: var(--text-soft);
        }

        .book-cover,
        .thumb-preview {
            width: 74px;
            height: 96px;
            object-fit: cover;
            border-radius: 0.85rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
        }

        .thumb-preview-lg {
            width: 140px;
            height: 188px;
        }

        .status-badge {
            border-radius: 999px;
            padding: 0.5rem 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .status-tersedia {
            color: #10291d;
            background: linear-gradient(135deg, #9df0c1, #6ee7b7);
        }

        .status-dipinjam {
            color: #3d2011;
            background: linear-gradient(135deg, #ffd6a1, #ffb86b);
        }

        .auth-wrapper {
            min-height: 100vh;
        }

        .auth-shell {
            max-width: 480px;
        }

        .floating-orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(18px);
            opacity: 0.35;
            pointer-events: none;
        }

        .orb-one {
            width: 180px;
            height: 180px;
            background: rgba(63, 179, 255, 0.28);
            top: 12%;
            left: 10%;
        }

        .orb-two {
            width: 220px;
            height: 220px;
            background: rgba(110, 231, 183, 0.18);
            bottom: 10%;
            right: 8%;
        }

        .upload-zone {
            border: 1.5px dashed rgba(255, 255, 255, 0.18);
            border-radius: 1rem;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.03);
            transition: border-color 0.22s ease, transform 0.22s ease, background-color 0.22s ease;
        }

        .upload-zone:hover {
            transform: translateY(-1px);
            border-color: rgba(63, 179, 255, 0.7);
        }

        .empty-panel {
            border: 1px dashed rgba(255, 255, 255, 0.16);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            color: var(--text-soft);
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(63, 179, 255, 0.28), rgba(110, 231, 183, 0.24));
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .topbar-meta {
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .pagination .page-link {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
        }

        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
        }

        @keyframes fadeSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero-panel {
                padding: 1.4rem;
            }
        }
    </style>
</head>
<body class="<?= e($bodyClassName) ?>">
<?php if (is_logged_in()): ?>
    <main class="container py-4 py-lg-5 position-relative">
        <div class="topbar-shell d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-badge">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <a href="index.php?route=books.index" class="text-decoration-none text-light fw-bold fs-5">Inventaris Perpustakaan</a>
                    <div class="topbar-meta">Kelola buku dengan tampilan yang lebih ringkas dan nyaman dibaca.</div>
                </div>
            </div>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                <span class="topbar-meta">Masuk sebagai <strong class="text-light"><?= e($_SESSION['user']['username']) ?></strong> • <?= is_admin() ? 'Admin' : 'User' ?></span>
                <form method="POST" action="index.php?route=logout" class="m-0">
                    <?= csrf_input() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm btn-soft">Keluar</button>
                </form>
            </div>
        </div>
<?php else: ?>
    <main class="container py-4 py-lg-5 position-relative">
<?php endif; ?>
    <?php if ($flashSuccess): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= e($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-danger border-0 shadow-sm"><?= e($flashError) ?></div>
    <?php endif; ?>
