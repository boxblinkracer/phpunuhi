<?php

namespace PHPUnuhi\Bundles\Translation;

interface TranslatorInterface
{

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     */
    function translate(string $text, string $sourceLocale, string $targetLocale): string;

}
