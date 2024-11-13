<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class KebabCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'kebab';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^([a-z](?![\d])|[\d](?![a-z]))+(-?([a-z](?![\d])|[\d](?![a-z])))*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
