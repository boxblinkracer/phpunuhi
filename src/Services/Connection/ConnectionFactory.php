<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Connection;

use PDO;

class ConnectionFactory
{
    public function buildMySQLConnectionString(string $host, string $port, string $database): string
    {
        return 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';collation=utf8mb4_unicode_ci';
    }


    public function fromConnectionString(string $connectionString, string $user, string $pwd): PDO
    {
        $pdo = new PDO($connectionString, $user, $pwd);

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }
}
