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
     * @var bool
     */
    private $isNested = true;


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
            $this->isNested,
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

        # its always nested, except if we explicitly set it to false
        $nested = $set->getAttributeValue('nested');
        $this->isNested = strtolower($nested) !== 'false';

        $this->loader = new JsonLoader();
        $this->saver = new JsonSaver((int)$indent, $sort, $eolLast);
    }

    /**
     * @param TranslationSet $set
     * @throws Exception
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $this->loader->loadTranslations($locale, $this->getHierarchy());
        }
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {
            $filename = $locale->getFilename();
            $translationCount += $this->saver->saveLocale($locale, $this->getHierarchy(), $filename);
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
        $translationsCount = $this->saver->saveLocale($locale, $this->getHierarchy(), $filename);

        return new StorageSaveResult(1, $translationsCount);
    }
}
