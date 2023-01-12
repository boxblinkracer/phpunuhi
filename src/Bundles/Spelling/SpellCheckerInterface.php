<?php

namespace PHPUnuhi\Bundles\Spelling;

interface SpellCheckerInterface
{

    /**
     * @param string $text
     * @param string $locale
     * @return bool
     */
    function validateSpelling(string $text, string $locale): bool;

    /**
     * @param string $text
     * @param string $locale
     * @return string
     */
    function fixSpelling(string $text, string $locale): string;

}
