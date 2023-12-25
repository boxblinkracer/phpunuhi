<?php

namespace PHPUnuhi\Services\CaseStyle;

class LowerCaseConverter
{

    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string
    {
        return strtolower($text);
    }
}
