<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\INI;

use Exception;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class IniStorage implements StorageInterface
{
    private ?bool $sortIni = null;

    private ?bool $eolLast = null;


    public function getStorageName(): string
    {
        return 'ini';
    }


    public function getFileExtension(): string
    {
        return 'ini';
    }


    public function supportsFilters(): bool
    {
        return false;
    }


    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            false,
            ''
        );
    }


    public function configureStorage(TranslationSet $set): void
    {
        $this->sortIni = filter_var($set->getAttributeValue('sort'), FILTER_VALIDATE_BOOLEAN);
        $this->eolLast = filter_var($set->getAttributeValue('eol-last'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @throws Exception
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $iniArray = parse_ini_file($locale->getFilename(), true, INI_SCANNER_RAW);

            if ($iniArray === false) {
                throw new Exception('Error when loading INI file: ' . $locale->getFilename());
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


    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $contentBuffer = [];

        $translationCount = $this->buildFileContentBuffer($locale, $contentBuffer, $filename);

        file_put_contents($filename, $contentBuffer);

        return new StorageSaveResult(1, $translationCount);
    }

    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult
    {
        $this->saveTranslationLocale($locale, $locale->getFilename());

        return new StorageSaveResult(1, 1);
    }

    /**
     * @param array<mixed> $contentBuffer
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

        foreach ($locale->getTranslations() as $id => $translation) {
            $preparedTranslations[$id] = $translation->getValue();
        }

        if ($this->sortIni === true) {
            ksort($preparedTranslations);
        }

        foreach ($preparedTranslations as $key => $value) {
            $content .= $key . '="' . $value . '"' . PHP_EOL;
            $translationCount++;
        }

        # last EOL is optional, so let's remove it first
        $content = rtrim($content, PHP_EOL);

        if ($this->eolLast === true) {
            $content .= PHP_EOL;
        }

        $contentBuffer[$locale->getFilename()] = $content;

        return $translationCount;
    }

    public function getContentFileTemplate(): string
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR . 'StorageFileTemplate.ini') ?: '';
    }
}
