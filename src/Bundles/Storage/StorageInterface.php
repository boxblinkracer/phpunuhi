<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{

    /**
     * @return bool
     */
    public function supportsFilters(): bool;

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslations(TranslationSet $set): void;

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult;

}