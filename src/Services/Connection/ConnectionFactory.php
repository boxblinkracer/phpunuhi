<?php

namespace PHPUnuhi\Services\Connection;

use PDO;

class ConnectionFactory
{

    /**
     * @param string $host
     * @param string $port
     * @param string $database
     * @return string
     */
    public function buildMySQLConnectionString(string $host, string $port, string $database): string
    {
        return 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8';
    }

    /**
     * @param string $connectionString
     * @param string $user
     * @param string $pwd
     * @return PDO
     */
    public function fromConnectionString(string $connectionString, string $user, string $pwd): PDO
    {
        $pdo = new PDO($connectionString, $user, $pwd);

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }
}
