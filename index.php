<?php

require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/schema.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Book.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/BookController.php';

try {
    $database = new Database();
    $connection = $database->connect();
    Schema::ensure($connection);
} catch (Throwable $exception) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi di config/database.php dan pastikan MySQL XAMPP aktif.');
}

$userModel = new User($connection);
$bookModel = new Book($connection);
$authController = new AuthController($userModel);
$bookController = new BookController($bookModel);

$route = $_GET['route'] ?? (is_logged_in() ? 'books.index' : 'login');

$routes = [
    'login' => [$authController, 'showLogin'],
    'login.submit' => [$authController, 'login'],
    'register' => [$authController, 'showRegister'],
    'register.submit' => [$authController, 'register'],
    'logout' => [$authController, 'logout'],
    'books.index' => [$bookController, 'index'],
    'books.create' => [$bookController, 'create'],
    'books.store' => [$bookController, 'store'],
    'books.edit' => [$bookController, 'edit'],
    'books.update' => [$bookController, 'update'],
    'books.delete' => [$bookController, 'destroy'],
    'books.borrow' => [$bookController, 'borrow'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}

call_user_func($routes[$route]);
