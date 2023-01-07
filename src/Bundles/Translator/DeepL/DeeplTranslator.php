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
     * @var bool
     */
    private $formality;


    /**
     * @param string $apiKey
     * @param bool $formality
     */
    public function __construct(string $apiKey, bool $formality)
    {
        $this->apiKey = $apiKey;
        $this->formality = $formality;
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
        $formalValue = ($this->formality) ? 'more' : 'less';

        $translator = new \DeepL\Translator($this->apiKey);

        if ($targetLanguage === 'en') {
            $targetLanguage = 'en-GB';
        }

        $result = $translator->translateText(
            $text,
            null,
            $targetLanguage,
            [
                'formality' => $formalValue,
            ]
        );

        if (is_array($result)) {
            return $result[0]->text;
        }

        return $result->text;
    }

}