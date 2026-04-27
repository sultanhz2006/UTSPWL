<?php

class Database
{
    private string $host = 'localhost';
    private string $username = 'root';
    private string $password = '';
    private string $database = 'uts_pwl';
    private int $port = 3306;

    public function connect(): mysqli
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database,
            $this->port
        );

        $connection->set_charset('utf8mb4');

        return $connection;
    }
}
