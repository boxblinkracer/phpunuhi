<?php

namespace PHPUnuhi\Bundles\Storage\INI;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
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
     * @return string
     */
    public function getStorageName(): string
    {
        return 'ini';
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'ini';
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
            false,
            ''
        );
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function configureStorage(TranslationSet $set): void
    {
        $this->sortIni = (bool)$set->getAttributeValue('sort');
    }

    /**
     * @param TranslationSet $set
     * @return void
     * @throws \Exception
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {

            $iniArray = parse_ini_file($locale->getFilename(), true, INI_SCANNER_RAW);

            if ($iniArray === false) {
                throw new \Exception('Error when loading INI file: ' . $locale->getFilename());
            }

            foreach ($iniArray as $key => $value) {

                if (is_array($value)) {
                    # we have a section
                    if ($key === $locale->getIniSection()) {
                        foreach ($value as $transKey => $transValue) {
                            $locale->addTranslation($transKey, $transValue, '');
                        }
                    }

                } else {
                    # we just have a plain value
                    $locale->addTranslation($key, $value, '');
                }
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

        $contentBuffer = [];

        foreach ($set->getLocales() as $locale) {
            $translationCount += $this->buildFileContentBuffer($locale, $contentBuffer, $locale->getFilename());
            $localeCount++;
        }

        # we need this, because our locales might be in 1 single INI file with sections
        # so we first build a basic content structure and then save it in
        # either 1 or multiple files.
        foreach ($contentBuffer as $filename => $content) {
            file_put_contents($filename, $content);
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
        $contentBuffer = [];

        $translationCount = $this->buildFileContentBuffer($locale, $contentBuffer, $filename);

        file_put_contents($filename, $contentBuffer);

        return new StorageSaveResult(1, $translationCount);
    }

    /**
     * @param Locale $locale
     * @param array<mixed> $contentBuffer
     * @param string $filename
     * @return int
     */
    public function buildFileContentBuffer(Locale $locale, array &$contentBuffer, string $filename): int
    {
        $content = "";
        $translationCount = 0;

        if (array_key_exists($filename, $contentBuffer)) {
            $content = $contentBuffer[$filename];
            $content .= PHP_EOL;
        }

        if ($locale->getIniSection() !== '') {
            $content .= "[" . $locale->getIniSection() . "]" . PHP_EOL;
            $content .= PHP_EOL;
        }

        $preparedTranslations = [];

        foreach ($locale->getTranslations() as $translation) {
            $preparedTranslations[$translation->getID()] = $translation->getValue();
        }

        if ($this->sortIni) {
            ksort($preparedTranslations);
        }

        foreach ($preparedTranslations as $key => $value) {
            $content .= $key . '="' . $value . '"' . PHP_EOL;
            $translationCount++;
        }

        $contentBuffer[$locale->getFilename()] = $content;

        return $translationCount;
    }

}
