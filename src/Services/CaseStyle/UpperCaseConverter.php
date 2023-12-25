<?php

namespace PHPUnuhi\Services\CaseStyle;

class UpperCaseConverter implements CaseStyleConverterInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'upper';
    }


    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string
    {
        return strtoupper($text);
    }
}
