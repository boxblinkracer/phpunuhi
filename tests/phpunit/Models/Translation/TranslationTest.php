<?php

namespace PHPUnuhi\Tests\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Translation;

class TranslationTest extends TestCase
{

    /**
     * @return void
     */
    public function testLocale()
    {
        $translation = new Translation('de-DE', 'title', 'Titel');

        $this->assertEquals('de-DE', $translation->getLocale());
    }

    /**
     * @return void
     */
    public function testKey()
    {
        $translation = new Translation('de-DE', 'title', 'Titel');

        $this->assertEquals('title', $translation->getKey());
    }

    /**
     * @return void
     */
    public function testValue()
    {
        $translation = new Translation('de-DE', 'title', 'Titel');

        $this->assertEquals('Titel', $translation->getValue());
    }

}
