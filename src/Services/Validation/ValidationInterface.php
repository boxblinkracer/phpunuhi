<?php

namespace PHPUnuhi\Services\Validation;
use PHPUnuhi\Models\Translation\TranslationSuite;

interface ValidationInterface
{

    /**
     * @param TranslationSuite $suite
     * @return bool
     */
    function validate(TranslationSuite $suite): bool;

}