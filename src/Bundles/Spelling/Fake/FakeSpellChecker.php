<?php

namespace PHPUnuhi\Bundles\Spelling\Fake;

use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;

class FakeSpellChecker implements SpellCheckerInterface
{

    /**
     * @param string $text
     * @param string $locale
     * @return bool
     */
    public function validateSpelling(string $text, string $locale): bool
    {
        return true;
    }

    /**
     * @param string $text
     * @param string $locale
     * @return string
     */
    public function fixSpelling(string $text, string $locale): string
    {
        return 'spellchecker-' . $text;
    }

}
