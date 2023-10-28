<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class TranslationSetTest extends TestCase
{

    /**
     * @return void
     */
    public function testName()
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $this->assertEquals('storefront', $set->getName());
    }

    /**
     * @return void
     */
    public function testFormat()
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];


        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $this->assertEquals('json', $set->getFormat());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testProtection()
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();

        $protection->addTerm('protected-word');

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, [], []);

        $this->assertEquals('protected-word', $set->getProtection()->getTerms()[0]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testRules()
    {
        $attributes = [];
        $filter = new Filter();
        $locales = [];
        $protection = new Protection();
        $rules = [
            new Rule('test-rule', true),
        ];

        $set = new TranslationSet('storefront', 'json', $protection, $locales, $filter, $attributes, [], $rules);

        $this->assertEquals('test-rule', $set->getRules()[0]->getName());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');
        $attributes[] = new Attribute('sort', 'true');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $expected = [
            new Attribute('indent', '2'),
            new Attribute('sort', 'true'),
        ];

        $this->assertEquals($expected, $set->getAttributes());
    }

    /**
     * @return void
     */
    public function testAttributeValue()
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $this->assertEquals('2', $set->getAttributeValue('indent'));
    }

    /**
     * @return void
     */
    public function testGetAttributeValueNotFound()
    {
        $attributes = [];
        $attributes[] = new Attribute('indent', '2');

        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $this->assertEquals('', $set->getAttributeValue('abc'));
    }

    /**
     * @return void
     */
    public function testGetLocales()
    {
        $attributes = [];
        $filter = new Filter();

        $locales = [];
        $locales[] = new Locale('', '', '');
        $locales[] = new Locale('', '', '');

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $this->assertCount(2, $set->getLocales());
    }

    /**
     * @return void
     * @throws \PHPUnuhi\Exceptions\TranslationNotFoundException
     */
    public function testFindAnyExistingTranslation()
    {
        $attributes = [];
        $filter = new Filter();


        $localeEN = new Locale('EN', '', '');

        $localeDE = new Locale('DE', '', '');
        $localeDE->addTranslation('btnCancel', 'Abbrechen', '');

        $locales = [$localeEN, $localeDE];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $existing = $set->findAnyExistingTranslation('btnCancel', '');

        $expected = [
            'locale' => 'DE',
            'translation' => $localeDE->findTranslation('btnCancel'), # we have to get the DE version
        ];

        $this->assertEquals($expected, $existing);
    }

    /**
     * @return void
     * @throws \PHPUnuhi\Exceptions\TranslationNotFoundException
     */
    public function testFindAnyExistingTranslationWithLocale()
    {
        $attributes = [];
        $filter = new Filter();


        $localeEN = new Locale('EN', '', '');
        $localeEN->addTranslation('btnCancel', 'Cancel', '');

        $localeDE = new Locale('DE', '', '');
        $localeDE->addTranslation('btnCancel', 'Abbrechen', '');

        $localeNL = new Locale('NL', '', '');
        $localeNL->addTranslation('btnCancel', 'Annuleren', '');

        $locales = [$localeEN, $localeDE, $localeNL];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $existing = $set->findAnyExistingTranslation('btnCancel', 'NL');

        $expected = [
            'locale' => 'NL',
            'translation' => $localeNL->findTranslation('btnCancel'),
        ];

        $this->assertEquals($expected, $existing);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testFindAnyExistingTranslationNotFound()
    {
        $this->expectException(TranslationNotFoundException::class);

        $attributes = [];
        $filter = new Filter();
        $locales = [];

        $set = new TranslationSet('storefront', 'json', new Protection(), $locales, $filter, $attributes, [], []);

        $set->findAnyExistingTranslation('abc', '');
    }

}
