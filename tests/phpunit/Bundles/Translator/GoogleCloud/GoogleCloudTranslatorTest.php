<?php

namespace phpunit\Bundles\Translator\GoogleCloud;

use Exception;
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
        $this->assertTrue($foundOptions[0]->hasValue());
    }

    /**
     * @throws \Exception
     * @return void
     */
    public function testSetOptions(): void
    {
        $options = [
            'google-key' => 'key-123',
        ];

        $translator = new GoogleCloudTranslator();
        $translator->setOptionValues($options);

        $this->assertEquals('key-123', $translator->getApiKey());
    }

    /**
     * @throws \Exception
     * @return void
     */
    public function testSetOptionsWithMissingKeyThrowsException(): void
    {
        $this->expectException(Exception::class);

        $options = [
            'google-key' => ' '
        ];

        $translator = new GoogleCloudTranslator();
        $translator->setOptionValues($options);
    }
}
