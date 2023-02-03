<?php

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;


use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

class ConstantCaseValidator implements CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'constant';
    }

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool
    {
        $regex = "/^([A-Z](?![\d])|[\d](?![A-Z]))+(_?([A-Z](?![\d])|[\d](?![A-Z])))*$|^$/";

        $count = preg_match($regex, $text);

        return ($count > 0);
    }

}