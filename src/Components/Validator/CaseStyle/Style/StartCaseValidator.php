<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class StartCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'start';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^(([A-Z][a-z]*|\\d+)[\\s])*([A-Z][a-z]*|\\d+)\$|^\$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
