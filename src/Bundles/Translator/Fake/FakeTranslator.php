<?php

namespace PHPUnuhi\Bundles\Translation\Fake;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class FakeTranslator implements TranslatorInterface
{

    /**
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return string
     */
    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        return $targetLanguage . '-' . $text;
    }

}
