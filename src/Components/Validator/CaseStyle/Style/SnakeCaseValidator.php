<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class SnakeCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'snake';
    }


    public function isValid(string $text): bool
    {
        $regex = "/^([a-z](?![\d])|[\d](?![a-z]))+(_?([a-z](?![\d])|[\d](?![a-z])))*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
