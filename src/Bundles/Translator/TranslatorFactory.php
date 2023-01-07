<?php

namespace PHPUnuhi\Bundles\Translation;

use PHPUnuhi\Bundles\Translation\DeepL\DeeplTranslator;
use PHPUnuhi\Bundles\Translation\Fake\FakeTranslator;
use PHPUnuhi\Bundles\Translation\GoogleCloud\GoogleCloudTranslator;

class TranslatorFactory
{

    /**
     * @param string $service
     * @param string $apiKey
     * @return TranslatorInterface
     * @throws \Exception
     */
    public static function fromService(string $service, string $apiKey): TranslatorInterface
    {
        switch (strtolower($service)) {

            case 'fake':
                return new FakeTranslator();

            case 'deepl':
                return new DeeplTranslator($apiKey);

            case 'google':
                return new GoogleCloudTranslator($apiKey);

            default:
                throw new \Exception('Translator service ' . $service . ' not found in PHPUnuhi');
        }
    }

}
