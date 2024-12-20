<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Translation;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;

class LocaleTest extends TestCase
{
    public function testName(): void
    {
        $locale = new Locale('en GB', false, 'de.json', 'de-section');

        $this->assertEquals('en GB', $locale->getName());
    }


    public function testIsBase(): void
    {
        $locale = new Locale('en GB', true, 'de.json', 'de-section');

        $this->assertEquals(true, $locale->isBase());
    }


    public function testFilename(): void
    {
        $locale = new Locale('en GB', false, './de.json', 'de-section');

        $this->assertEquals('./de.json', $locale->getFilename());
    }


    public function testIniSection(): void
    {
        $locale = new Locale('en GB', false, 'de.json', 'de-section');

        $this->assertEquals('de-section', $locale->getIniSection());
    }


    public function testExchangeIdentifierFilename(): void
    {
        $locale = new Locale('', false, 'de.json', '');

        $this->assertEquals('de.json', $locale->getExchangeIdentifier());
    }


    public function testExchangeIdentifierName(): void
    {
        $locale = new Locale('DE', false, 'de.json', '');

        $this->assertEquals('DE', $locale->getExchangeIdentifier());
    }


    public function testExchangeIdentifierHasNoSpaces(): void
    {
        $locale = new Locale('en GB', false, 'de.json', '');

        $this->assertEquals('en-GB', $locale->getExchangeIdentifier());
    }


    public function testGetTranslationKeys(): void
    {
        $locale = new Locale('', false, '', '');
        $locale->addTranslation('title', 'Titel', '');
        # add description twice
        $locale->addTranslation('description', '', '');
        $locale->addTranslation('description', '', '');

        $this->assertCount(2, $locale->getTranslationIDs());
    }


    public function testGetValidTranslations(): void
    {
        $locale = new Locale('', false, '', '');
        # valid
        $locale->addTranslation('title', 'Titel', '');
        # invalid
        $locale->addTranslation('description', '', '');

        $this->assertCount(1, $locale->getValidTranslations());
    }


    public function testGetTranslations(): void
    {
        $locale = new Locale('', false, '', '');
        $locale->addTranslation('title', 'Titel', '');
        $locale->addTranslation('description', '', '');

        $this->assertCount(2, $locale->getTranslations());
    }


    public function testSetTranslations(): void
    {
        $locale = new Locale('', false, '', '');

        $this->assertCount(0, $locale->getTranslations());

        $translations = [];
        $translations[''] = new Translation('', '', '');

        $locale->setTranslations($translations);

        $this->assertCount(1, $locale->getTranslations());
    }

    public function testSetLineNumbers(): void
    {
        $locale = new Locale('', false, '', '');

        $this->assertCount(0, $locale->getLineNumbers());

        $lineNumbers = [];
        $lineNumbers['title'] = 1;

        $locale->setLineNumbers($lineNumbers);

        $this->assertCount(1, $locale->getLineNumbers());
    }

    public function testGetLineNumbers(): void
    {
        $locale = new Locale('', false, '', '');

        $lineNumbers = [
            'title' => 1,
            'button' => 2,
        ];
        $locale->setLineNumbers($lineNumbers);

        $this->assertCount(2, $locale->getLineNumbers());
    }

    public function testFindLineNumber(): void
    {
        $locale = new Locale('', false, '', '');

        $lineNumbers = [
            'title' => 1,
            'button' => 2,
        ];
        $locale->setLineNumbers($lineNumbers);

        $this->assertEquals(1, $locale->findLineNumber('title'));
    }

    /**
     * This test verifies that we do not add translations twice,
     * but do correctly update them in this case.
     *
     * @throws TranslationNotFoundException
     */
    public function testAddTranslationAvoidDuplicates(): void
    {
        $localeEN = new Locale('EN', false, '', '');

        $this->assertCount(0, $localeEN->getTranslationIDs());

        $localeEN->addTranslation('btnCancel', 'Cancel', '');

        $this->assertCount(1, $localeEN->getTranslationIDs());

        $localeEN->addTranslation('btnCancel', 'Abbrechen', '');

        $this->assertCount(1, $localeEN->getTranslationIDs());
        $this->assertEquals('Abbrechen', $localeEN->findTranslation('btnCancel')->getValue());
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testUpdateTranslationKey(): void
    {
        $locale = new Locale('EN', false, '', '');
        $locale->addTranslation('btnCancel', 'Cancel', '');

        $locale->updateTranslationKey('btnCancel', 'btn-cancel');

        $translation = $locale->findTranslation('btn-cancel');

        $this->assertCount(1, $locale->getTranslationIDs());
        $this->assertEquals('btn-cancel', $translation->getID());
        $this->assertEquals('Cancel', $translation->getValue());
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function testUpdateTranslationKeyIfNewKeyExists(): void
    {
        $this->expectException(Exception::class);

        $locale = new Locale('EN', false, '', '');
        $locale->addTranslation('btnCancel', 'Cancel', '');
        $locale->addTranslation('btn-cancel', 'Abbrechen', '');

        $locale->updateTranslationKey('btnCancel', 'btn-cancel');

        $this->assertCount(1, $locale->getTranslationIDs());
    }
}
