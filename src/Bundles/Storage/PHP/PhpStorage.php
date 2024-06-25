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
use PHPUnuhi\Traits\AutoCreateTranslationFileTrait;

class PhpStorage implements StorageInterface
{
    use ArrayTrait;
    use AutoCreateTranslationFileTrait;

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
     * @var bool
     */
    private $eolLast;


    /**
     *
     */
    public function __construct()
    {
        $this->loader = new PHPLoader();
    }

    /**
     * @return string
     */
    public function getStorageName(): string
    {
        return 'php';
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
    public function configureStorage(TranslationSet $set): void
    {
        $this->sort = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $this->eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);

        $this->saver = new PHPSaver($this->eolLast);

        $this->commandPrompt = $set->getCommandPrompt();
        $this->setStorageFileTemplate((string) file_get_contents(__DIR__.'/Template/StorageFileTemplate.php'));
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            if ($this->ensureTranslationFileExists($locale)) {
                $this->loader->loadTranslation($locale, $this->getHierarchy()->getDelimiter());
            }
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
