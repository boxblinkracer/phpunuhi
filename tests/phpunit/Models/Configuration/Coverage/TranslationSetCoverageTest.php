<?php

namespace phpunit\Models\Configuration\Coverage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;

class TranslationSetCoverageTest extends TestCase
{

    /**
     * @return void
     */
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
     * @param float $expected
     * @param float $input
     * @return void
     */
    public function testSetMinCoverageIsMaximum100(float $expected, float $input): void
    {
        $cov = new TranslationSetCoverage();

        $cov->setMinCoverage($input);

        $this->assertEquals($expected, $cov->getMinCoverage());
    }

    /**
     * @return void
     */
    public function testHasMinCoverage(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasMinCoverage());

        $cov->setMinCoverage(80);

        $this->assertTrue($cov->hasMinCoverage());
    }

    /**
     * @return void
     */
    public function testGetLocaleCoverages(): void
    {
        $cov = new TranslationSetCoverage();

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
        $cov = new TranslationSetCoverage();

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
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasLocaleCoverages());

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverages());
    }

    /**
     * @return void
     */
    public function testHasLocaleCoverage(): void
    {
        $cov = new TranslationSetCoverage();

        $this->assertFalse($cov->hasLocaleCoverage('DE'));

        $cov->addLocaleCoverage('DE', 80);

        $this->assertTrue($cov->hasLocaleCoverage('DE'));
        $this->assertFalse($cov->hasLocaleCoverage('EN'));
    }
}
