<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class PascalCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'pascal';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^[A-Z]+([A-Z]*([a-z]|[\d](?![a-z]))*)+$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
