<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

interface StorageInterface
{
    /**
     * Returns a unique name for the storage.
     */
    public function getStorageName(): string;

    /**
     * Returns the file extension of the storage.
     * Just use an empty string if you are building a non-file based storage.
     *
     */
    public function getFileExtension(): string;

    /**
     * Returns if field-level filtering is allowed.
     * This might only be appropriate when reading database tables.
     *
     */
    public function supportsFilters(): bool;


    public function getHierarchy(): StorageHierarchy;

    /**
     * Sets configuration options for your storage.
     * We cannot use the constructor for it because of the registering and loading inside the factory.
     *
     */
    public function configureStorage(TranslationSet $set): void;

    /**
     * This function will load all translations from the provided set.
     * Every locale should be iterated and depending on its file/database settings, the
     * translations should be loaded from it.
     *
     */
    public function loadTranslationSet(TranslationSet $set): void;

    /**
     * This function should save the whole translation-set according to its configuration.
     * Every locale should be saved accordingly.
     *
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult;

    /**
     * This function should only save the provided locale to the provided filename.
     * This is e.g. used for migrations and other specific use cases.
     *
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult;

    /**
     * This function should only save the provided translation to the provided locale.
     * This can be used if you immediately want to update and store translations without
     * waiting for certain processes to complete.
     */
    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult;

    /**
     * Get the content file template to create automate files.
     *
     * @return string The content file template.
     */
    public function getContentFileTemplate(): string;
}
