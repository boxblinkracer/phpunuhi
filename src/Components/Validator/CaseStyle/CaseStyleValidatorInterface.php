<?php

namespace PHPUnuhi\Components\Validator\CaseStyle;

interface CaseStyleValidatorInterface
{

    /**
     * @return string
     */
    public function getIdentifier():string;

    /**
     * @param string $text
     * @return bool
     */
    public function isValid(string $text): bool;

}