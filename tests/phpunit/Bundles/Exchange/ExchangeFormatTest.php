<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Exchange;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;

class ExchangeFormatTest extends TestCase
{
    public function testFormatCSV(): void
    {
        $this->assertEquals('csv', ExchangeFormat::CSV);
    }


    public function testFormatHTML(): void
    {
        $this->assertEquals('html', ExchangeFormat::HTML);
    }
}
