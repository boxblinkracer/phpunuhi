<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class CamelCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'camel';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^[a-z]+([A-Z]*([a-z]|[\d](?![a-z]))*)+$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
