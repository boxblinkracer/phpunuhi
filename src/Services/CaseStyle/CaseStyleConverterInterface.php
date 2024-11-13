<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\CaseStyle;

interface CaseStyleConverterInterface
{
    public function getIdentifier(): string;


    public function convert(string $text): string;
}
