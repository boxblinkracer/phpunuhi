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
    public function testBuildMySQLConnectionString(): void
    {
        $host = 'localhost';
        $port = '3306';
        $database = 'test_db';

        $result = (new ConnectionFactory())->buildMySQLConnectionString($host, $port, $database);

        $expected = 'mysql:host=localhost;port=3306;dbname=test_db;charset=utf8';

        $this->assertEquals($expected, $result);
    }

    /**
     * @return void
     */
    public function testConnectionRefused(): void
    {
        $this->expectExceptionMessage('SQLSTATE[HY000] [2002] Connection refused');

        $factory = new ConnectionFactory();
        $connStr = $factory->buildMySQLConnectionString('127.0.01', '3306', 'test');

        $factory->fromConnectionString($connStr, '', '');
    }

    /**
     * @return void
     */
    public function testConnectionSuccessful(): void
    {
        $factory = new ConnectionFactory();
        $conn = $factory->fromConnectionString('sqlite::memory:', '', '');

        $this->assertInstanceOf(PDO::class, $conn);
    }
}
