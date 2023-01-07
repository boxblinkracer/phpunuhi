<?php

namespace PHPUnuhi\Bundles\Translation;

interface TranslatorInterface
{

    /**
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return string
     */
    function translate(string $text, string $sourceLanguage, string $targetLanguage): string;

}
