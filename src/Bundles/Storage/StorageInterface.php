<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{

    /**
     * Returns the file extension of the storage.
     * Just use an empty string if you are building a non-file based storage.
     *
     * @return string
     */
    public function getFileExtension(): string;

    /**
     * Returns if field-level filtering is allowed.
     * This might only be appropriate when reading database tables.
     *
     * @return bool
     */
    public function supportsFilters(): bool;

    /**
     * @return StorageHierarchy
     */
    public function getHierarchy(): StorageHierarchy;

    /**
     * This function will load all translations from the provided set.
     * Every locale should be iterated and depending on its file/database setttings, the
     * translations should be loaded from it.
     *
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set): void;

    /**
     * This function should save the whole translation-set according to its configuration.
     * Every locale should be saved accordingly.
     *
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult;

    /**
     * This function should only save the provided locale to the provided filename.
     * This is e.g. used for migrations and other specific use cases.
     *
     * @param Locale $locale
     * @param string $filename
     * @return StorageSaveResult
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult;

}
