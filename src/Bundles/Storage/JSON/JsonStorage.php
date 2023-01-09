<?php

namespace PHPUnuhi\Bundles\Storage\JSON;

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
     * @param int $indent
     * @param bool $sort
     */
    public function __construct(int $indent, bool $sort)
    {
        $this->loader = new JsonLoader();
        $this->saver = new JsonSaver($indent, $sort);
    }

    /**
     * @param Locale $locale
     * @return void
     * @throws \Exception
     */
    public function loadTranslations(Locale $locale): void
    {
        $this->loader->loadTranslations($locale);
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        return $this->saver->saveTranslations($set);
    }

}