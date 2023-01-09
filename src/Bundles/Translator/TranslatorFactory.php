<?php

namespace PHPUnuhi\Bundles\Translation;

use PHPUnuhi\Bundles\Translation\DeepL\DeeplTranslator;
use PHPUnuhi\Bundles\Translation\Fake\FakeTranslator;
use PHPUnuhi\Bundles\Translation\GoogleCloud\GoogleCloudTranslator;
use PHPUnuhi\Bundles\Translation\GoogleWeb\GoogleWebTranslator;
use PHPUnuhi\Bundles\Translation\OpenAI\OpenAITranslator;

class TranslatorFactory
{

    /**
     * @param string $service
     * @param string $apiKey
     * @param bool $formal
     * @return TranslatorInterface
     * @throws \Exception
     */
    public static function fromService(string $service, string $apiKey, bool $formal): TranslatorInterface
    {
        switch (strtolower($service)) {

            case 'fake':
                return new FakeTranslator();

            case 'deepl':
                return new DeeplTranslator($apiKey, $formal);

            case 'googlecloud':
                return new GoogleCloudTranslator($apiKey);

            case 'googleweb':
                return new GoogleWebTranslator();

            case 'openai':
                return new OpenAITranslator($apiKey);

            default:
                throw new \Exception('Translator service ' . $service . ' not found in PHPUnuhi');
        }
    }

}
