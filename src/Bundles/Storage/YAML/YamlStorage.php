<?php

namespace PHPUnuhi\Bundles\Storage\YAML;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlLoader;
use PHPUnuhi\Bundles\Storage\YAML\Services\YamlSaver;
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
     * @param bool $sort
     */
    public function __construct(int $indent, bool $sort)
    {
        $this->loader = new YamlLoader();
        $this->saver = new YamlSaver($indent, $sort);
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

    public function loadTranslations(TranslationSet $set): void
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        foreach ($set->getLocales() as $locale) {
            $this->loader->loadTranslations($locale, $delimiter);
        }
    }

    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $delimiter = $this->getHierarchy()->getDelimiter();

        return $this->saver->saveTranslations($set, $delimiter);
    }
}
