<?php

namespace PHPUnuhi\Bundles\Storage\JSON;

use Exception;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonLoader;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class JsonStorage implements StorageInterface
{

    /**
     * @var JsonLoader
     */
    private $loader;

    /**
     * @var JsonSaver
     */
    private $saver;


    /**
     * @return string
     */
    public function getStorageName(): string
    {
        return 'json';
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'json';
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
        $indent = $set->getAttributeValue('indent');
        $indent = ($indent === '') ? '2' : $indent;
        $sort = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);

        $this->loader = new JsonLoader();
        $this->saver = new JsonSaver((int)$indent, $sort, $eolLast);
    }

    /**
     * @param TranslationSet $set
     * @return void
     * @throws Exception
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        foreach ($set->getLocales() as $locale) {
            $this->loader->loadTranslations($locale, $delimiter);
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
            $translationCount += $this->saver->saveLocale($locale, $delimiter, $filename);
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

        $translationsCount = $this->saver->saveLocale($locale, $delimiter, $filename);

        return new StorageSaveResult(1, $translationsCount);
    }

}
