<?php

namespace phpunit\Models\Configuration\Coverage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage\Coverage;

class CoverageTest extends TestCase
{

    /**
     * @return void
     */
    public function testSetTotalMinCoverage(): void
    {
        $cov = new Coverage();

        $cov->setTotalMinCoverage(80);

        $this->assertEquals(80, $cov->getTotalMinCoverage());
    }

    /**
     * @return void
     */
    public function testHasTotalMinCoverage(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasTotalMinCoverage());

        $cov->setTotalMinCoverage(80);

        $this->assertTrue($cov->hasTotalMinCoverage());
    }

    /**
     * @return void
     */
    public function testGetLocaleCoverages(): void
    {
        $cov = new Coverage();

        $this->assertCount(0, $cov->getLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);
        $cov->addLocaleCoverage('EN', 80);

        $this->assertCount(2, $cov->getLocaleCoverages());
    }

    /**
     * @return void
     */
    public function testGetLocaleCoverageByName(): void
    {
        $cov = new Coverage();

        $cov->addLocaleCoverage('DE', 35);
        $cov->addLocaleCoverage('EN', 80);

        $locale = $cov->getLocaleCoverage('DE');

        $this->assertEquals('DE', $locale->getLocale());
        $this->assertEquals(35, $locale->getMinCoverage());
    }

    /**
     * @return void
     */
    public function testHasLocaleCoverages(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverages());
    }

    /**
     * @return void
     */
    public function testHasLocaleCoverage(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasLocaleCoverage('DE'));

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverage('DE'));
        $this->assertFalse($cov->hasLocaleCoverage('EN'));
    }
}
