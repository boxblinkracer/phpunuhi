<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class SnakeCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'snake';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^([a-z](?![\d])|[\d](?![a-z]))+(_?([a-z](?![\d])|[\d](?![a-z])))*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }
}
