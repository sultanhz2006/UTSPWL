<?php

class User
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function findByUsername(string $username): ?array
    {
        $statement = $this->connection->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        $statement->bind_param('s', $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function existsByUsername(string $username): bool
    {
        $statement = $this->connection->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $statement->bind_param('s', $username);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return (bool) $result;
    }

    public function create(string $username, string $passwordHash): void
    {
        $role = 'user';
        $statement = $this->connection->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $statement->bind_param('sss', $username, $passwordHash, $role);
        $statement->execute();
        $statement->close();
    }
}
