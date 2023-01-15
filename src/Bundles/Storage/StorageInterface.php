<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{

    /**
     * @param TranslationSet $set
     * @return void
     */
    function loadTranslations(TranslationSet $set): void;

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    function saveTranslations(TranslationSet $set): StorageSaveResult;

}