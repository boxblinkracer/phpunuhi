<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\YAML;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlLoader;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlSaver;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class YamlStorage implements StorageInterface
{
    use ArrayTrait;

    private YamlLoader $loader;
    private YamlSaver $saver;


    public function getStorageName(): string
    {
        return 'yaml';
    }

    public function getFileExtension(): string
    {
        return 'yaml';
    }

    public function supportsFilters(): bool
    {
        return false;
    }

    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            true,
            '.'
        );
    }

    public function configureStorage(TranslationSet $set): void
    {
        $indent = $set->getAttributeValue('indent');
        $indent = ($indent === '') ? '2' : $indent;
        $sort = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);

        $this->loader = new YamlLoader();
        $this->saver = new YamlSaver((int)$indent, $sort, $eolLast);
    }

    public function loadTranslationSet(TranslationSet $set): void
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        foreach ($set->getLocales() as $locale) {
            $this->loader->loadLocale($locale, $delimiter);
        }
    }

    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {
            $filename = $locale->getFilename();
            $translationCount += $this->saver->saveTranslations($locale, $delimiter, $filename);
            $localeCount++;
        }

        return new StorageSaveResult($localeCount, $translationCount);
    }

    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        $translationCount = $this->saver->saveTranslations($locale, $delimiter, $filename);

        return new StorageSaveResult(1, $translationCount);
    }

    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult
    {
        $this->saveTranslationLocale($locale, $locale->getFilename());

        return new StorageSaveResult(1, 1);
    }

    public function getContentFileTemplate(): string
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR . 'StorageFileTemplate.yaml') ?: '';
    }
}
