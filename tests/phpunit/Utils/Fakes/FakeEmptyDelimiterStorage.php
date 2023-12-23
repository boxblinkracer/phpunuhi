<?php

namespace phpunit\Utils\Fakes;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class FakeEmptyDelimiterStorage implements StorageInterface
{
    public function getStorageName(): string
    {
        return 'empty-delimiter';
    }

    public function getFileExtension(): string
    {
        // TODO: Implement getFileExtension() method.
    }

    public function supportsFilters(): bool
    {
        // TODO: Implement supportsFilters() method.
    }

    /**
     * @return StorageHierarchy
     */
    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            true,
            ''
        );
    }

    public function configureStorage(TranslationSet $set): void
    {
        // TODO: Implement configureStorage() method.
    }

    public function loadTranslationSet(TranslationSet $set): void
    {
        // TODO: Implement loadTranslationSet() method.
    }

    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        // TODO: Implement saveTranslationSet() method.
    }

    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        // TODO: Implement saveTranslationLocale() method.
    }
}
