<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\CaseStyle;

class LowerCaseConverter implements CaseStyleConverterInterface
{
    public function getIdentifier(): string
    {
        return 'lower';
    }


    public function convert(string $text): string
    {
        return strtolower($text);
    }
}
