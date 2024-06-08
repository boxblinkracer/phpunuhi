<?php

namespace PHPUnuhi\Tests\Components\Repoter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Components\Reporter\ReporterFactory;
use PHPUnuhi\Exceptions\ConfigurationException;

class ReporterFactoryTest extends TestCase
{

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testEmptyReportNameThrowsException(): void
    {
        $factory = ReporterFactory::getInstance();

        $this->expectException(InvalidArgumentException::class);

        $factory->getReporter('');
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testUnknownReporterThrowsException(): void
    {
        $factory = ReporterFactory::getInstance();

        $this->expectException(ConfigurationException::class);

        $factory->getReporter('unknown');
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testJUnitReporterIsFound(): void
    {
        $factory = ReporterFactory::getInstance();

        $reporter = $factory->getReporter('junit');

        $this->assertInstanceOf(JUnitReporter::class, $reporter);
    }
}
