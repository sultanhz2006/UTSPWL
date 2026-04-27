<?php

class Book
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function paginate(int $limit, int $offset): array
    {
        $statement = $this->connection->prepare('
            SELECT id, judul, pengarang, tahun, stok, status, gambar, thumbpath, dipinjam_oleh, dipinjam_pada, created_at
            FROM buku
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ');
        $statement->bind_param('ii', $limit, $offset);
        $statement->execute();
        $result = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return $result;
    }

    public function find(int $id): ?array
    {
        $statement = $this->connection->prepare('SELECT id, judul, pengarang, tahun, stok, status, gambar, thumbpath, dipinjam_oleh, dipinjam_pada FROM buku WHERE id = ? LIMIT 1');
        $statement->bind_param('i', $id);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return $result ?: null;
    }

    public function create(array $data): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO buku (judul, pengarang, tahun, stok, status, gambar, thumbpath, dipinjam_oleh, dipinjam_pada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $borrowedBy = $data['dipinjam_oleh'] ?? null;
        $borrowedAt = $data['dipinjam_pada'] ?? null;
        $statement->bind_param(
            'ssiisssss',
            $data['judul'],
            $data['pengarang'],
            $data['tahun'],
            $data['stok'],
            $data['status'],
            $data['gambar'],
            $data['thumbpath'],
            $borrowedBy,
            $borrowedAt
        );
        $statement->execute();
        $statement->close();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->connection->prepare('
            UPDATE buku
            SET judul = ?, pengarang = ?, tahun = ?, stok = ?, status = ?, gambar = ?, thumbpath = ?, dipinjam_oleh = ?, dipinjam_pada = ?
            WHERE id = ?
        ');
        $borrowedBy = $data['dipinjam_oleh'] ?? null;
        $borrowedAt = $data['dipinjam_pada'] ?? null;
        $statement->bind_param(
            'ssiisssssi',
            $data['judul'],
            $data['pengarang'],
            $data['tahun'],
            $data['stok'],
            $data['status'],
            $data['gambar'],
            $data['thumbpath'],
            $borrowedBy,
            $borrowedAt,
            $id
        );
        $statement->execute();
        $statement->close();
    }

    public function borrow(int $id, string $username): bool
    {
        $statement = $this->connection->prepare('
            UPDATE buku
            SET
                stok = stok - 1,
                status = CASE WHEN stok - 1 > 0 THEN "Tersedia" ELSE "Dipinjam" END,
                dipinjam_oleh = ?,
                dipinjam_pada = NOW()
            WHERE id = ? AND stok > 0
        ');
        $statement->bind_param('si', $username, $id);
        $statement->execute();
        $affectedRows = $statement->affected_rows;
        $statement->close();

        return $affectedRows > 0;
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM buku WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        $statement->close();
    }

    public function totalBooks(): int
    {
        $result = $this->connection->query('SELECT COUNT(*) AS total FROM buku')->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }

    public function totalStock(): int
    {
        $result = $this->connection->query('SELECT SUM(stok) AS total FROM buku')->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }

    public function availableBooks(): int
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) AS total FROM buku WHERE status = ?');
        $status = 'Tersedia';
        $statement->bind_param('s', $status);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();

        return (int) ($result['total'] ?? 0);
    }
}
