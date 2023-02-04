<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class KebabCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'kebab';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^([a-z](?![\d])|[\d](?![a-z]))+(-?([a-z](?![\d])|[\d](?![a-z])))*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}