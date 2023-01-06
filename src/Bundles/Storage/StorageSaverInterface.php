<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageSaverInterface
{

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    function saveTranslations(TranslationSet $set): StorageSaveResult;

}
