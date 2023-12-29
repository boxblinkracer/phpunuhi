<?php

namespace phpunit\Bundles\Translator\GoogleWeb;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\GoogleWeb\GoogleWebTranslator;

class GoogleWebTranslatorTest extends TestCase
{


    /**
     * @return void
     */
    public function testGetName(): void
    {
        $translator = new GoogleWebTranslator();

        $this->assertEquals('googleweb', $translator->getName());
    }

    /**
     * @return void
     */
    public function testGetOptions(): void
    {
        $translator = new GoogleWebTranslator();

        $foundOptions = $translator->getOptions();

        $this->assertCount(0, $foundOptions);
    }
}
