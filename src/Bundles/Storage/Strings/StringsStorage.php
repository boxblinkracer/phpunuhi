<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Strings;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class StringsStorage implements StorageInterface
{
    use StringTrait;

    public function getStorageName(): string
    {
        return 'strings';
    }

    public function getFileExtension(): string
    {
        return "strings";
    }

    public function supportsFilters(): bool
    {
        return false;
    }

    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(false, '');
    }

    public function configureStorage(TranslationSet $set): void
    {
    }

    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $lines = file($locale->getFilename());
            if ($lines === []) {
                continue;
            }
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $translation = $this->getTranslationFromLine($line);

                if ($translation instanceof Translation) {
                    $locale->addTranslation($translation->getKey(), $translation->getValue(), '');
                }
            }
        }
    }

    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        foreach ($set->getLocales() as $locale) {
            $this->writeLocale($locale);
        }

        return new StorageSaveResult(0, 0);
    }

    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $this->writeLocale($locale);

        return new StorageSaveResult(0, 0);
    }


    private function writeLocale(Locale $locale): void
    {
        $lines = file($locale->getFilename());

        if ($lines === []) {
            return;
        }

        $newContent = '';

        $processedTranslationKeys = [];

        /** @var string[] $lines */
        foreach ($lines as $line) {
            $lineTranslation = $this->getTranslationFromLine($line);

            if ($lineTranslation instanceof Translation) {
                $translationFound = false;
                foreach ($locale->getTranslations() as $translation) {
                    if ($translation->getKey() === $lineTranslation->getKey()) {
                        $newContent .= "\"{$translation->getKey()}\" = \"{$translation->getValue()}\";\n";
                        $translationFound = true;

                        $processedTranslationKeys[] = $translation->getKey();
                        break;
                    }
                }

                if (!$translationFound) {
                    $newContent .= $line . "\n";
                }
            } else {
                $newContent .= $line;
            }
        }

        if ($locale->getTranslations() !== []) {
            $newContent .= "\n";

            foreach ($locale->getTranslations() as $translation) {
                if (!in_array($translation->getKey(), $processedTranslationKeys)) {
                    $newContent .= "\"{$translation->getKey()}\" = \"{$translation->getValue()}\";\n";
                }
            }
        }

        file_put_contents($locale->getFilename(), $newContent);
    }

    private function getTranslationFromLine(string $line): ?Translation
    {
        $line = trim($line);

        if (preg_match('/"(.+)"\s*=\s*"(.*)";/', $line, $matches)) {
            $key = trim($matches[1]);
            $value = $matches[2];

            return new Translation($key, $value, '');
        }

        return null;
    }
}
