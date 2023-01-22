<?php

namespace phpunit\Bundles\Translator\DeepL;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\DeepL\DeeplTranslator;

class DeepLTranslatorTest extends TestCase
{


    /**
     * @return void
     */
    public function testAllowedFormality()
    {
        $expected = [
            'de',
            'nl',
            'fr',
            'it',
            'pl',
            'ru',
            'es',
            'pt'
        ];

        $this->assertEquals($expected, DeeplTranslator::ALLOWED_FORMALITY);
    }

}
