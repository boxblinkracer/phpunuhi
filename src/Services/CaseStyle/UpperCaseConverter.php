<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\CaseStyle;

class UpperCaseConverter implements CaseStyleConverterInterface
{
    public function getIdentifier(): string
    {
        return 'upper';
    }



    public function convert(string $text): string
    {
        return strtoupper($text);
    }
}
