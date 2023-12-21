<?php

namespace PHPUnuhi\Services\Connection;

use PDO;

class ConnectionFactory
{

    /**
     * @return PDO
     */
    public function pdoFromEnv(): PDO
    {
        $host = (string)getenv('DB_HOST');
        $port = (string)getenv('DB_PORT');
        $user = (string)getenv('DB_USER');
        $pwd = (string)getenv('DB_PASSWD');
        $dbName = (string)getenv('DB_DBNAME');

        $pdo = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbName . ';charset=utf8', $user, $pwd);

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");

        return $pdo;
    }
}
