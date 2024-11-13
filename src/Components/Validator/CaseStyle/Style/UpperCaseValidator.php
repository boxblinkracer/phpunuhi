<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class UpperCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'upper';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^[^\sa-z]+[^\sa-z]*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
