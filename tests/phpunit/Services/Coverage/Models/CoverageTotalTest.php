<?php

namespace phpunit\Services\Coverage\Models;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Coverage\Models\CoverageLocale;
use PHPUnuhi\Services\Coverage\Models\CoverageTotal;
use PHPUnuhi\Services\Coverage\Models\CoverageTranslationSet;

class CoverageTotalTest extends TestCase
{

    /**
     * @var CoverageTranslationSet[]
     */
    private $sets;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $locale1 = new Locale('en', 'English', '');
        $locale1->addTranslation('title', 'Title Title', '');
        $locale1->addTranslation('text2', '', '');

        $locale2 = new Locale('de', 'German', '');
        $locale2->addTranslation('title', 'Title Title Car', '');
        $locale2->addTranslation('text2', '', '');

        $this->sets[] = new CoverageTranslationSet(
            'Storefront',
            [
                new CoverageLocale($locale1),
            ]
        );

        $this->sets[] = new CoverageTranslationSet(
            'Admin',
            [
                new CoverageLocale($locale2)
            ]
        );
    }

    /**
     * @return void
     */
    public function testCoverageSets(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getTranslationSetCoverages();

        $this->assertCount(2, $value);
    }

    /**
     * @return void
     */
    public function testCountAll(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCountAll();

        $this->assertEquals(4, $value);
    }

    /**
     * @return void
     */
    public function testCountTranslated(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCountTranslated();

        $this->assertEquals(2, $value);
    }

    /**
     * @return void
     */
    public function testWordCount(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getWordCount();

        $this->assertEquals(5, $value);
    }

    /**
     * @return void
     */
    public function testCoverage(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCoverage();

        $this->assertEquals(50, $value);
    }
}
