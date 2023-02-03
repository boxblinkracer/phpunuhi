<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class LowerCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'lower';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^[^\sA-Z]+[^\sA-Z]*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}