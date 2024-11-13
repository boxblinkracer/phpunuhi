<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class NumberCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'number';
    }


    public function isValid(string $text): bool
    {
        return is_numeric($text);
    }
}
