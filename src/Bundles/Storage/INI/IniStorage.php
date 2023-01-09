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

        $iniArray = parse_ini_file($locale->getFilename(), true, INI_SCANNER_RAW);
        
        if ($iniArray === false) {
            throw new \Exception('Error when loading INI file: ' . $locale->getFilename());
        }

        foreach ($iniArray as $key => $value) {

            if (is_array($value)) {
                # we have a section
                if ($key === $locale->getIniSection()) {
                    foreach ($value as $transKey => $transValue) {
                        $locale->addTranslation($transKey, $transValue);
                    }
                }

            } else {
                # we just have a plain value
                $locale->addTranslation($key, $value);
            }
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


        $fileContents = [];


        foreach ($set->getLocales() as $locale) {

            $content = "";

            if (array_key_exists($locale->getFilename(), $fileContents)) {
                $content = $fileContents[$locale->getFilename()];
                $content .= PHP_EOL;
            }


            if ($locale->getIniSection() !== '') {
                $content .= "[" . $locale->getIniSection() . "]" . PHP_EOL;
                $content .= PHP_EOL;
            }

            $localeCount++;


            $preparedTranslations = [];

            foreach ($locale->getTranslations() as $translation) {
                $preparedTranslations[$translation->getKey()] = $translation->getValue();
            }

            if ($this->sortIni) {
                ksort($preparedTranslations);
            }

            foreach ($preparedTranslations as $key => $value) {

                $content .= $key . '="' . $value . '"' . PHP_EOL;

                $translationCount++;
            }

            $fileContents[$locale->getFilename()] = $content;
        }


        foreach ($fileContents as $filename => $content) {
            file_put_contents($filename, $content);
        }

        return new StorageSaveResult($localeCount, $translationCount);
    }

}