<?php

namespace phpunit\Services\Connection;

use PDO;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Connection\ConnectionFactory;

class ConnectionFactoryTest extends TestCase
{

    /**
     * @return void
     */
    public function testPdoObjectBuiltCorrectly(): void
    {
        putenv('DB_HOST=127.0.01');
        putenv('DB_PORT=3306');
        putenv('DB_USER=test');
        putenv('DB_PASSWD=test');
        putenv('DB_DBNAME=test');

        $this->expectExceptionMessage('SQLSTATE[HY000] [2002] Connection refused');

        $factory = new ConnectionFactory();
        $conn = $factory->pdoFromEnv();

        $this->assertInstanceOf(PDO::class, $conn);
    }
}
