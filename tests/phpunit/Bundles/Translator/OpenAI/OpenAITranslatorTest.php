<?php

namespace PHPUnuhi\Tests\Bundles\Translator\OpenAI;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\OpenAI\OpenAITranslator;

class OpenAITranslatorTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $translator = new OpenAITranslator();

        $this->assertEquals('openai', $translator->getName());
    }

    /**
     * @return void
     */
    public function testGetOptions(): void
    {
        $translator = new OpenAITranslator();

        $foundOptions = $translator->getOptions();

        $this->assertEquals('openai-key', $foundOptions[0]->getName());
        $this->assertEquals(true, $foundOptions[0]->hasValue());
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testSetOptionsWithMissingKeyThrowsException(): void
    {
        $this->expectException(Exception::class);

        $options = [
            'openai-key' => ' '
        ];

        $translator = new OpenAITranslator();
        $translator->setOptionValues($options);
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testSetOptions(): void
    {
        $options = [
            'openai-key' => 'key-123',
        ];

        $translator = new OpenAITranslator();
        $translator->setOptionValues($options);

        $this->assertEquals('key-123', $translator->getApiKey());
    }
}
