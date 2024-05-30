<?php

namespace phpunit\Services\Coverage\Models;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Coverage\Models\CoverageLocale;

class CoverageLocaleTest extends TestCase
{

    /**
     * @var Locale
     */
    private $locale;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->locale = new Locale('en', false, 'English', '');

        $this->locale->addTranslation('title', 'Title Title', '');
        $this->locale->addTranslation('subtitle', 'Subtitle', '');
        $this->locale->addTranslation('text', 'Test', '');
        $this->locale->addTranslation('text2', '', '');
    }

    /**
     * @return void
     */
    public function testName(): void
    {
        $coverage = new CoverageLocale($this->locale);

        $value = $coverage->getLocaleName();

        $this->assertEquals('en', $value);
    }

    /**
     * @return void
     */
    public function testCoverage(): void
    {
        $coverage = new CoverageLocale($this->locale);

        $value = $coverage->getCoverage();

        $this->assertEquals(75, $value);
    }

    /**
     * @return void
     */
    public function testWordCount(): void
    {
        $coverage = new CoverageLocale($this->locale);

        $value = $coverage->getWordCount();

        $this->assertEquals(4, $value);
    }

    /**
     * @return void
     */
    public function testCountAll(): void
    {
        $coverage = new CoverageLocale($this->locale);

        $value = $coverage->getCountAll();

        $this->assertEquals(4, $value);
    }

    /**
     * @return void
     */
    public function testCountTranslated(): void
    {
        $coverage = new CoverageLocale($this->locale);

        $value = $coverage->getCountTranslated();

        $this->assertEquals(3, $value);
    }
}
