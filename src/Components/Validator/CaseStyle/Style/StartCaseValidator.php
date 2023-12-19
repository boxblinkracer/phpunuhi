<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class StartCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'start';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^(([A-Z][a-z]*|\\d+)[\\s])*([A-Z][a-z]*|\\d+)\$|^\$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}