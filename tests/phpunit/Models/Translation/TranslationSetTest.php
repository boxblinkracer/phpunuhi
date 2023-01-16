<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\Filter;
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

        $set = new TranslationSet('storefront', 'json', $locales, $filter, $attributes);

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


        $set = new TranslationSet('storefront', 'json', $locales, $filter, $attributes);

        $this->assertEquals('json', $set->getFormat());
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

        $set = new TranslationSet('storefront', 'json', $locales, $filter, $attributes);

        $this->assertEquals('2', $set->getAttributeValue('indent'));
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

        $set = new TranslationSet('storefront', 'json', $locales, $filter, $attributes);

        $this->assertCount(2, $set->getLocales());
    }

}
