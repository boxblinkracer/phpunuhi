<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;

class CoverageTest extends TestCase
{
    public function testSetMinCoverage(): void
    {
        $cov = new Coverage();

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
        $cov = new Coverage();

        $cov->setMinCoverage($input);

        $this->assertEquals($expected, $cov->getMinCoverage());
    }


    public function testHasMinCoverage(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasMinCoverage());

        $cov->setMinCoverage(80);

        $this->assertTrue($cov->hasMinCoverage());
    }


    public function testHasLocaleCoverages(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverages());
    }


    public function testHasLocaleCoverageByName(): void
    {
        $cov = new Coverage();

        $this->assertFalse($cov->hasLocaleCoverage('DE'));

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverage('DE'));
        $this->assertFalse($cov->hasLocaleCoverage('EN'));
    }


    public function testGetLocaleCoverages(): void
    {
        $cov = new Coverage();

        $this->assertCount(0, $cov->getLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);
        $cov->addLocaleCoverage('EN', 80);

        $this->assertCount(2, $cov->getLocaleCoverages());
    }


    public function testGetLocaleCoverageByName(): void
    {
        $cov = new Coverage();

        $cov->addLocaleCoverage('DE', 35);
        $cov->addLocaleCoverage('EN', 80);

        $locale = $cov->getLocaleCoverage('DE');

        $this->assertEquals('DE', $locale->getLocale());
        $this->assertEquals(35, $locale->getMinCoverage());
    }


    public function testHasTranslationSetCoverage(): void
    {
        $cov = new Coverage();

        $tCov = new TranslationSetCoverage();
        $tCov->setMinCoverage(50);

        $this->assertFalse($cov->hasTranslationSetCoverage('Admin'));

        $cov->addTranslationSetCoverage('Admin', $tCov);

        $this->assertTrue($cov->hasTranslationSetCoverage('Admin'));
    }


    public function testGetTranslationSetCoverage(): void
    {
        $cov = new Coverage();

        $tCov = new TranslationSetCoverage();
        $tCov->setMinCoverage(50);

        $cov->addTranslationSetCoverage('Admin', $tCov);

        $this->assertSame($tCov, $cov->getTranslationSetCoverage('Admin'));
    }
}
