<?php

namespace phpunit\Models\Configuration\Coverage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage\LocaleCoverage;

class LocaleCoverageTest extends TestCase
{

    /**
     * @return void
     */
    public function testLocale(): void
    {
        $cov = new LocaleCoverage('DE', 35);

        $this->assertEquals('DE', $cov->getLocale());
    }

    /**
     * @return void
     */
    public function testMinCoverage(): void
    {
        $cov = new LocaleCoverage('DE', 35);

        $this->assertEquals(35, $cov->getMinCoverage());
    }
}
