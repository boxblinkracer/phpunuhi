<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class LowerCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'lower';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^[^\sA-Z]+[^\sA-Z]*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
