<?php

namespace PHPUnuhi\Bundles\Translation\Fake;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class FakeTranslator implements TranslatorInterface
{

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        return $targetLocale . '-' . $text;
    }

}
