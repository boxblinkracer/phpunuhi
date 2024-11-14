<?php

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class XmlStorage implements StorageInterface
{
    public function getStorageName(): string
    {
        return "xml";
    }

    public function getFileExtension(): string
    {
        return "xml";
    }

    public function supportsFilters(): bool
    {
        return false;
    }

    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(false, '');
    }

    public function configureStorage(TranslationSet $set): void
    {

    }

    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $locale->addTranslation('key1', 'value1', '');
        }
    }

    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {

    }

    public function saveTranslationLocale(\PHPUnuhi\Models\Translation\Locale $locale, string $filename): StorageSaveResult
    {
    }


}