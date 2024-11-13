<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class FakeStorage implements StorageInterface
{
    public function getStorageName(): string
    {
        return 'fake';
    }

    public function getFileExtension(): string
    {
        return '.fake';
    }

    public function supportsFilters(): bool
    {
        return true;
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
    }

    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        return new StorageSaveResult(0, 0);
    }

    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        return new StorageSaveResult(0, 0);
    }

    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult
    {
        return new StorageSaveResult(0, 0);
    }

    public function getContentFileTemplate(): string
    {
        return 'fake/content.fake';
    }
}
