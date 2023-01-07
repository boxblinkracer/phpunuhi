<?php

namespace PHPUnuhi\Tests\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Translation;

class TranslationTest extends TestCase
{

    /**
     * @return void
     */
    public function testKey()
    {
        $translation = new Translation('title', 'Titel');

        $this->assertEquals('title', $translation->getKey());
    }

    /**
     * @return void
     */
    public function testValue()
    {
        $translation = new Translation('title', 'Titel');

        $this->assertEquals('Titel', $translation->getValue());
    }

    /**
     * @return void
     */
    public function testIsEmptyWithSpaces()
    {
        $translation = new Translation('title', '   ');

        $this->assertEquals(true, $translation->isEmpty());
    }

}
