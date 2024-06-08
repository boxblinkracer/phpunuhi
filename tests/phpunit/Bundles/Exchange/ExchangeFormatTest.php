<?php

namespace PHPUnuhi\Tests\Bundles\Exchange;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;

class ExchangeFormatTest extends TestCase
{

    /**
     * @return void
     */
    public function testFormatCSV(): void
    {
        $this->assertEquals('csv', ExchangeFormat::CSV);
    }

    /**
     * @return void
     */
    public function testFormatHTML(): void
    {
        $this->assertEquals('html', ExchangeFormat::HTML);
    }
}
