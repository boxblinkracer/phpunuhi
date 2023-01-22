<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;

class LocaleTest extends TestCase
{


    /**
     * @return void
     */
    public function testName()
    {
        $locale = new Locale('en GB', 'de.json', 'de-section');

        $this->assertEquals('en GB', $locale->getName());
    }

    /**
     * @return void
     */
    public function testFilename()
    {
        $locale = new Locale('en GB', './de.json', 'de-section');

        $this->assertEquals('./de.json', $locale->getFilename());
    }

    /**
     * @return void
     */
    public function testIniSection()
    {
        $locale = new Locale('en GB', 'de.json', 'de-section');

        $this->assertEquals('de-section', $locale->getIniSection());
    }

    /**
     * @return void
     */
    public function testExchangeIdentifierFilename()
    {
        $locale = new Locale('', 'de.json', '');

        $this->assertEquals('de.json', $locale->getExchangeIdentifier());
    }

    /**
     * @return void
     */
    public function testExchangeIdentifierName()
    {
        $locale = new Locale('DE', 'de.json', '');

        $this->assertEquals('DE', $locale->getExchangeIdentifier());
    }

    /**
     * @return void
     */
    public function testExchangeIdentifierHasNoSpaces()
    {
        $locale = new Locale('en GB', 'de.json', '');

        $this->assertEquals('en-GB', $locale->getExchangeIdentifier());
    }

    /**
     * @return void
     */
    public function testGetTranslationKeys()
    {
        $locale = new Locale('', '', '');
        $locale->addTranslation('title', 'Titel', '');
        # add description twice
        $locale->addTranslation('description', '', '');
        $locale->addTranslation('description', '', '');

        $this->assertCount(2, $locale->getTranslationIDs());
    }

    /**
     * @return void
     */
    public function testGetValidTranslations()
    {
        $locale = new Locale('', '', '');
        # valid
        $locale->addTranslation('title', 'Titel', '');
        # invalid
        $locale->addTranslation('description', '', '');

        $this->assertCount(1, $locale->getValidTranslations());
    }

    /**
     * @return void
     */
    public function testGetTranslations()
    {
        $locale = new Locale('', '', '');
        $locale->addTranslation('title', 'Titel', '');
        $locale->addTranslation('description', '', '');

        $this->assertCount(2, $locale->getTranslations());
    }

    /**
     * @return void
     */
    public function testSetTranslations()
    {
        $locale = new Locale('', '', '');

        $this->assertCount(0, $locale->getTranslations());

        $translations = [];
        $translations[] = new Translation('', '', '');

        $locale->setTranslations($translations);

        $this->assertCount(1, $locale->getTranslations());
    }

}