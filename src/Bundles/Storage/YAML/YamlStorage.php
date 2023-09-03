<?php

namespace PHPUnuhi\Bundles\Storage\YAML;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlLoader;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlSaver;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class YamlStorage implements StorageInterface
{
    use ArrayTrait;

    /**
     * @var YamlLoader
     */
    private $loader;

    /**
     * @var YamlSaver
     */
    private $saver;


    /**
     * @return string
     */
    public function getStorageName(): string
    {
        return 'yaml';
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'yaml';
    }

    /**
     * @return bool
     */
    public function supportsFilters(): bool
    {
        return false;
    }

    /**
     * @return StorageHierarchy
     */
    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            true,
            '.'
        );
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function configureStorage(TranslationSet $set): void
    {
        $indent = $set->getAttributeValue('yamlIndent');
        $indent = ($indent === '') ? '2' : $indent;
        $sort = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);

        $this->loader = new YamlLoader();
        $this->saver = new YamlSaver((int)$indent, $sort, $eolLast);
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        foreach ($set->getLocales() as $locale) {
            $this->loader->loadLocale($locale, $delimiter);
        }
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
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

    /**
     * @param Locale $locale
     * @param string $filename
     * @return StorageSaveResult
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        $translationCount = $this->saver->saveTranslations($locale, $delimiter, $filename);

        return new StorageSaveResult(1, $translationCount);
    }

}
