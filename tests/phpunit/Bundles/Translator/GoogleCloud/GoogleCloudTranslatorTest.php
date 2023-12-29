<?php

namespace phpunit\Bundles\Translator\GoogleCloud;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\GoogleCloud\GoogleCloudTranslator;

class GoogleCloudTranslatorTest extends TestCase
{


    /**
     * @return void
     */
    public function testGetName(): void
    {
        $translator = new GoogleCloudTranslator();

        $this->assertEquals('googlecloud', $translator->getName());
    }

    /**
     * @return void
     */
    public function testGetOptions(): void
    {
        $translator = new GoogleCloudTranslator();

        $foundOptions = $translator->getOptions();

        $this->assertEquals('google-key', $foundOptions[0]->getName());
        $this->assertEquals(true, $foundOptions[0]->hasValue());
    }
}
