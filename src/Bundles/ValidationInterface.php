<?php

namespace PHPUnuhi\Bundles;

use PHPUnuhi\Models\Translation\TranslationSet;

interface ValidationInterface
{

    /**
     * @param TranslationSet $suite
     * @return bool
     */
    function validate(TranslationSet $suite): bool;

}
