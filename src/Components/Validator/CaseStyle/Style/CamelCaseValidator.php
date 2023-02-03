<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class CamelCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'camel';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^[a-z]+([A-Z]*([a-z]|[\d](?![a-z]))*)+$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}