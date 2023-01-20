<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{

    /**
     * @return bool
     */
    function supportsFilters(): bool;

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