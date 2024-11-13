<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration\Coverage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;

class TranslationSetCoverageTest extends TestCase
{
    public function testSetMinCoverage(): void
    {
        $cov = new TranslationSetCoverage();

        $cov->setMinCoverage(80);

        $this->assertEquals(80, $cov->getMinCoverage());
    }

    /**
     * @testWith    [ 99.9, 99.9 ]
     *               [ 100.0, 100.0 ]
     *               [ 100.0, 100.1 ]
     *
     */
    public function testSetMinCoverageIsMaximum100(float $expected, float $input): void
    {
        $cov = new TranslationSetCoverage();

        $cov->setMinCoverage($input);

        $this->assertEquals($expected, $cov->getMinCoverage());
    }


    public function testHasMinCoverage(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasMinCoverage());

        $cov->setMinCoverage(80);

        $this->assertTrue($cov->hasMinCoverage());
    }


    public function testGetLocaleCoverages(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertCount(0, $cov->getLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);
        $cov->addLocaleCoverage('EN', 80);

        $this->assertCount(2, $cov->getLocaleCoverages());
    }


    public function testGetLocaleCoverageByName(): void
    {
        $cov = new TranslationSetCoverage();

        $cov->addLocaleCoverage('DE', 35);
        $cov->addLocaleCoverage('EN', 80);

        $locale = $cov->getLocaleCoverage('DE');

        $this->assertEquals('DE', $locale->getLocale());
        $this->assertEquals(35, $locale->getMinCoverage());
    }


    public function testHasLocaleCoverages(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverages());
    }


    public function testHasLocaleCoverage(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasLocaleCoverage('DE'));

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverage('DE'));
        $this->assertFalse($cov->hasLocaleCoverage('EN'));
    }
}
