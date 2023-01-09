<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class TranslationSetTest extends TestCase
{

    /**
     * @return void
     */
    public function testName()
    {
        $set = new TranslationSet('storefront', 'json', 0, true, []);

        $this->assertEquals('storefront', $set->getName());
    }

    /**
     * @return void
     */
    public function testFormat()
    {
        $set = new TranslationSet('storefront', 'json', 0, true, []);

        $this->assertEquals('json', $set->getFormat());
    }

    /**
     * @return void
     */
    public function testJsonIndent()
    {
        $set = new TranslationSet('storefront', 'json', 2, true, []);

        $this->assertEquals(2, $set->getJsonIndent());
    }

    /**
     * @return void
     */
    public function testSort()
    {
        $set = new TranslationSet('storefront', 'json', 2, true, []);

        $this->assertEquals(true, $set->isSortStorage());
    }

    /**
     * @return void
     */
    public function testGetLocales()
    {
        $locales = [];
        $locales[] = new Locale('', '', '');
        $locales[] = new Locale('', '', '');

        $set = new TranslationSet('storefront', 'json', 0, true, $locales);

        $this->assertCount(2, $set->getLocales());
    }

}
