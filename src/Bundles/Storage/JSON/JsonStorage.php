<?php

namespace PHPUnuhi\Bundles\Storage\JSON;

use PHPUnuhi\Bundles\Storage\JSON\Services\JsonLoader;
use PHPUnuhi\Bundles\Storage\JSON\Services\JsonSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
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
     * @param int $indent
     * @param bool $sort
     */
    public function __construct(int $indent, bool $sort)
    {
        $this->loader = new JsonLoader();
        $this->saver = new JsonSaver($indent, $sort);
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
     * @throws \Exception
     */
    public function loadTranslations(TranslationSet $set): void
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
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        return $this->saver->saveTranslations($set, $delimiter);
    }

}
