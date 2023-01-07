<?php

namespace PHPUnuhi\Bundles\Translation\DeepL;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class DeeplTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return string
     * @throws \DeepL\DeepLException
     */
    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        $translator = new \DeepL\Translator($this->apiKey);

        if ($targetLanguage === 'en') {
            $targetLanguage = 'en-GB';
        }

        $result = $translator->translateText($text, null, $targetLanguage);

        if (is_array($result)) {
            return $result[0]->text;
        }

        return $result->text;
    }

}