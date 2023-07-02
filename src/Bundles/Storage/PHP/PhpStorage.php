<?php

namespace PHPUnuhi\Bundles\Storage\PHP;

use PHPUnuhi\Bundles\Storage\PHP\Services\PHPLoader;
use PHPUnuhi\Bundles\Storage\PHP\Services\PHPSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class PhpStorage implements StorageInterface
{

    use ArrayTrait;

    /**
     * @var PHPSaver
     */
    private $saver;

    /**
     * @var PHPLoader
     */
    private $loader;

    /**
     * @var bool
     */
    private $sort;


    /**
     * @param bool $sort
     */
    public function __construct(bool $sort)
    {
        $this->sort = $sort;

        $this->saver = new PHPSaver();
        $this->loader = new PHPLoader();
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'php';
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
    public function loadTranslationSet(TranslationSet $set): void
    {
        $this->loader->loadTranslationSet($set, $this->getHierarchy()->getDelimiter());
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

            $translationCount += $this->saver->saveLocale(
                $locale,
                $filename,
                $this->getHierarchy()->getDelimiter(),
                $this->sort
            );

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
        $translationCount = $this->saver->saveLocale(
            $locale,
            $filename,
            $this->getHierarchy()->getDelimiter(),
            $this->sort
        );

        return new StorageSaveResult(1, $translationCount);
    }

}
