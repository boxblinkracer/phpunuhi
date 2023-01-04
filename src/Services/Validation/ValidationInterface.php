<?php

namespace PHPUnuhi\Services\Validation;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ValidationInterface
{

    /**
     * @param TranslationSet $suite
     * @return bool
     */
    function validate(TranslationSet $suite): bool;

}