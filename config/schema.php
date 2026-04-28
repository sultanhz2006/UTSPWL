<?php

class Schema
{
    public static function ensure(mysqli $connection): void
    {
        $connection->query('CREATE DATABASE IF NOT EXISTS uts_pwl');
        $connection->select_db('uts_pwl');

        $connection->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        self::ensureColumn($connection, 'users', 'role', "ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user' AFTER password");
        self::ensureColumn($connection, 'users', 'email', "ALTER TABLE users ADD COLUMN email VARCHAR(150) DEFAULT NULL AFTER role");

        self::ensureColumn($connection, 'buku', 'dipinjam_oleh', "ALTER TABLE buku ADD COLUMN dipinjam_oleh VARCHAR(50) DEFAULT NULL AFTER thumbpath");
        self::ensureColumn($connection, 'buku', 'dipinjam_pada', "ALTER TABLE buku ADD COLUMN dipinjam_pada DATETIME DEFAULT NULL AFTER dipinjam_oleh");

        $connection->query("
            CREATE TABLE IF NOT EXISTS buku (
                id INT AUTO_INCREMENT PRIMARY KEY,
                judul VARCHAR(150) NOT NULL,
                pengarang VARCHAR(120) NOT NULL,
                tahun YEAR NOT NULL,
                stok INT NOT NULL DEFAULT 0,
                status ENUM('Tersedia', 'Dipinjam') NOT NULL DEFAULT 'Tersedia',
                gambar VARCHAR(255) DEFAULT NULL,
                thumbpath VARCHAR(255) DEFAULT NULL,
                dipinjam_oleh VARCHAR(50) DEFAULT NULL,
                dipinjam_pada DATETIME DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $defaultUsername = 'admin';
        $defaultPassword = '$2y$10$aBCtRoG1VJ2r7rELWID8je0Eb0CyUsWWIidAp9AQFQGdZF7nZGIDi';

        $checkStatement = $connection->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $checkStatement->bind_param('s', $defaultUsername);
        $checkStatement->execute();
        $existingUser = $checkStatement->get_result()->fetch_assoc();
        $checkStatement->close();

        if (!$existingUser) {
            $role = 'admin';
            $insertStatement = $connection->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $insertStatement->bind_param('sss', $defaultUsername, $defaultPassword, $role);
            $insertStatement->execute();
            $insertStatement->close();
        } else {
            $role = 'admin';
            $updateStatement = $connection->prepare('UPDATE users SET password = ?, role = ? WHERE username = ?');
            $updateStatement->bind_param('sss', $defaultPassword, $role, $defaultUsername);
            $updateStatement->execute();
            $updateStatement->close();
        }

        $connection->query("UPDATE users SET role = 'user' WHERE username <> 'admin' AND (role IS NULL OR role <> 'admin')");
    }

    private static function ensureColumn(mysqli $connection, string $table, string $column, string $alterSql): void
    {
        $statement = $connection->prepare("
            SELECT COUNT(*) AS total
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ");
        $statement->bind_param('ss', $table, $column);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        if ((int) ($result['total'] ?? 0) === 0) {
            $connection->query($alterSql);
        }
    }
}
