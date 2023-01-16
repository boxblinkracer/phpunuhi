<?php

namespace PHPUnuhi\Services\Connection;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{

    /**
     * @return Connection
     * @throws \Doctrine\DBAL\Exception
     */
    public function fromEnv(): Connection
    {
        $config = new Configuration();

        $params = [
            'host' => (string)getenv('DB_HOST'),
            'port' => (string)getenv('DB_PORT'),
            'user' => (string)getenv('DB_USER'),
            'password' => (string)getenv('DB_PASSWD'),
            'dbname' => (string)getenv('DB_DBNAME'),
            'driver' => 'pdo_mysql',
        ];

        /** @phpstan-ignore-next-line */
        return DriverManager::getConnection($params, $config);
    }

}