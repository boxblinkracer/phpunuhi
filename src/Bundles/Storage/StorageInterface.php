<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{

    /**
     * @param Locale $locale
     * @return void
     */
    function loadTranslations(Locale $locale): void;

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    function saveTranslations(TranslationSet $set): StorageSaveResult;

}