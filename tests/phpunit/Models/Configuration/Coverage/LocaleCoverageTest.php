<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration\Coverage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage\LocaleCoverage;

class LocaleCoverageTest extends TestCase
{
    public function testLocale(): void
    {
        $cov = new LocaleCoverage('DE', 35);

        $this->assertEquals('DE', $cov->getLocale());
    }


    public function testMinCoverage(): void
    {
        $cov = new LocaleCoverage('DE', 35);

        $this->assertEquals(35, $cov->getMinCoverage());
    }

    /**
     * @testWith    [ 99.9, 99.9 ]
     *               [ 100.0, 100.0 ]
     *               [ 100.0, 100.1 ]
     *
     */
    public function testSetMinCoverageIsMaximum100(float $expected, float $input): void
    {
        $cov = new LocaleCoverage('DE', $input);

        $this->assertEquals($expected, $cov->getMinCoverage());
    }
}
