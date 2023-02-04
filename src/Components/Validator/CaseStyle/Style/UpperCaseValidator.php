<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class UpperCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'upper';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^[^\sa-z]+[^\sa-z]*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}