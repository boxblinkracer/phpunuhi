<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\PHP;

use PHPUnuhi\Bundles\Storage\PHP\Services\PHPLoader;
use PHPUnuhi\Bundles\Storage\PHP\Services\PHPSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class PhpStorage implements StorageInterface
{
    use ArrayTrait;

    private PHPSaver $saver ;

    private PHPLoader $loader;

    /**
     * @var bool
     */
    private $sort;

    /**
     * @var bool
     */
    private $eolLast;



    public function __construct()
    {
        $this->loader = new PHPLoader();
    }


    public function getStorageName(): string
    {
        return 'php';
    }


    public function getFileExtension(): string
    {
        return 'php';
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
        $this->sort = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $this->eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);

        $this->saver = new PHPSaver($this->eolLast);
    }


    public function loadTranslationSet(TranslationSet $set): void
    {
        $this->loader->loadTranslationSet($set, $this->getHierarchy()->getDelimiter());
    }


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

    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult
    {
        $this->saveTranslationLocale($locale, $locale->getFilename());

        return new StorageSaveResult(1, 1);
    }
}
