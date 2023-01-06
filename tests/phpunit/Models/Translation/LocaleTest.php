<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Locale;

class LocaleTest extends TestCase
{

    /**
     * @return void
     */
    public function testExchangeIdentifierFilename()
    {
        $locale = new Locale('', 'de.json');

        $this->assertEquals('de.json', $locale->getExchangeIdentifier());
    }

    /**
     * @return void
     */
    public function testExchangeIdentifierName()
    {
        $locale = new Locale('DE', 'de.json');

        $this->assertEquals('DE', $locale->getExchangeIdentifier());
    }

}