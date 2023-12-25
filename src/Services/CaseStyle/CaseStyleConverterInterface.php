<?php

namespace PHPUnuhi\Services\CaseStyle;

interface CaseStyleConverterInterface
{

    /**
     * @return string
     */
    public function getIdentifier():string;

    /**
     * @param string $text
     * @return string
     */
    public function convert(string $text): string;
}
