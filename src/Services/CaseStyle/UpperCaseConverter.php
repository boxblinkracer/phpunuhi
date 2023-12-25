<?php

namespace PHPUnuhi\Services\CaseStyle;

class UpperCaseConverter
{

    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string
    {
        return strtoupper($text);
    }
}
