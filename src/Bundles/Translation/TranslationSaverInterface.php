<?php

namespace PHPUnuhi\Bundles\Translation;

use PHPUnuhi\Models\Translation\TranslationSet;

interface TranslationSaverInterface
{

    /**
     * @param TranslationSet $set
     * @return TranslateSaveResult
     */
    function save(TranslationSet $set): TranslateSaveResult;

}
