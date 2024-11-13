<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Coverage\Models;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Coverage\Models\CoverageLocale;
use PHPUnuhi\Services\Coverage\Models\CoverageTotal;
use PHPUnuhi\Services\Coverage\Models\CoverageTranslationSet;
use RuntimeException;

class CoverageTotalTest extends TestCase
{
    /**
     * @var CoverageTranslationSet[]
     */
    private array $sets;



    public function setUp(): void
    {
        $locale1 = new Locale('en', false, 'English', '');
        $locale1->addTranslation('title', 'Title Title', '');
        $locale1->addTranslation('text2', '', '');

        $locale2 = new Locale('de', false, 'German', '');
        $locale2->addTranslation('title', 'Title Title Car', '');
        $locale2->addTranslation('text2', '', '');

        $locale3 = new Locale('it', false, 'Italian', '');

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

        $this->sets[] = new CoverageTranslationSet(
            'Service',
            [
                new CoverageLocale($locale3)
            ]
        );
    }


    public function testCoverageSets(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getTranslationSetCoverages();

        $this->assertCount(3, $value);
    }


    public function testCountAll(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCountAll();

        $this->assertEquals(4, $value);
    }


    public function testCountTranslated(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCountTranslated();

        $this->assertEquals(2, $value);
    }


    public function testWordCount(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getWordCount();

        $this->assertEquals(5, $value);
    }


    public function testCoverage(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getCoverage();

        $this->assertEquals(50, $value);
    }


    /**
     * @return array<mixed>
     */
    public function getLocaleCoverageData(): array
    {
        return [
            [33.33, 'de'],
            [50, 'en'],
        ];
    }

    /**
     * @dataProvider getLocaleCoverageData
     *
     */
    public function testLocaleCoverage(float $expected, string $locale): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getLocaleCoverage($locale);

        $this->assertEquals($expected, $value);
    }

    /**
     * If we have a locale with no words, the coverage should be 100%.
     * Then this is fine.
     */
    public function testCoverageWithNoWordsIs100(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getLocaleCoverage('it');

        $this->assertEquals(100, $value);
    }

    /**
     * If we have a locale that is not in the sets, the coverage should be 0%.
     */
    public function testCoverageOfMissingLocaleIsZero(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $value = $coverage->getLocaleCoverage('missing');

        $this->assertEquals(0, $value);
    }


    public function testTranslationSetCoverage(): void
    {
        $coverage = new CoverageTotal($this->sets);

        $result = $coverage->getTranslationSetCoverage('Storefront');

        $this->assertEquals(50.0, $result->getCoverage());
    }


    public function testTranslationSetCoverageNotFound(): void
    {
        $this->expectException(RuntimeException::class);

        $coverage = new CoverageTotal($this->sets);

        $coverage->getTranslationSetCoverage('missing');
    }
}
