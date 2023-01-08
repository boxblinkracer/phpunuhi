<?php

namespace PHPUnuhi\Bundles\Storage\INI;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class IniStorage implements StorageInterface
{

    /**
     * @var bool
     */
    private $sortIni;


    /**
     * @param bool $sortIni
     */
    public function __construct(bool $sortIni)
    {
        $this->sortIni = $sortIni;
    }


    /**
     * @param Locale $locale
     * @return void
     */
    public function loadTranslations(Locale $locale): void
    {
        if (!file_exists($locale->getFilename())) {
            throw new \Exception('Attention, translation file not found: ' . $locale->getFilename());
        }

        $iniArray = parse_ini_file($locale->getFilename());

        if ($iniArray === false) {
            throw new \Exception('Error when loading INI file: ' . $locale->getFilename());
        }

        foreach ($iniArray as $key => $value) {
            $locale->addTranslation($key, $value);
        }
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {

            $localeCount++;

            $content = "";

            $preparedTranslations = [];

            foreach ($locale->getTranslations() as $translation) {
                $preparedTranslations[$translation->getKey()] = $translation->getValue();
            }

            if ($this->sortIni) {
                ksort($preparedTranslations);
            }

            foreach ($preparedTranslations as $key => $value) {

                $content .= $key . '=' . $value . PHP_EOL;

                $translationCount++;
            }

            file_put_contents($locale->getFilename(), $content);
        }

        return new StorageSaveResult($localeCount, $translationCount);
    }

}