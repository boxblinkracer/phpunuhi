<?php

namespace PHPUnuhi\Services\CaseStyle;

class LowerCaseConverter implements CaseStyleConverterInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'lower';
    }

    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string
    {
        return strtolower($text);
    }
}
