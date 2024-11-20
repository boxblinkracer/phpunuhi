<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\RESX;

use Exception;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use SimpleXMLElement;

class ResxStorage implements StorageInterface
{
    public function getStorageName(): string
    {
        return "resx";
    }


    public function getFileExtension(): string
    {
        return "resx";
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

    /**
     * @throws Exception
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $xmlContent = (string)file_get_contents($locale->getFilename());

            $xml = new SimpleXMLElement($xmlContent);
            $xml->registerXPathNamespace('x', 'http://schemas.microsoft.com/winfx/2006/xaml');

            /** @var SimpleXMLElement[] $nodes */
            $nodes = $xml->xpath('//x:data');

            foreach ($nodes as $entry) {
                $name = (string)$entry['name'];
                $type = (string)$entry['type'];

                // Only process entries that do not have a <type> node (indicating it's a non-image text)
                if (($type === '' || $type === '0') && (property_exists($entry, 'value') && $entry->value !== null)) {
                    $value = (string)$entry->value;
                    $locale->addTranslation($name, $value, '');
                }
            }
        }
    }


    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        $totalCount = 0;

        foreach ($set->getLocales() as $locale) {
            $totalCount += $this->saveTranslationLocale($locale, $locale->getFilename());
        }

        return new StorageSaveResult(count($set->getLocales()), $totalCount);
    }


    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        $totalCount = $this->writeResxFile($filename, $locale->getTranslations());

        return new StorageSaveResult(1, $totalCount);
    }

    public function saveTranslation(Translation $translation, Locale $locale): StorageSaveResult
    {
        $this->saveTranslationLocale($locale, $locale->getFilename());

        return new StorageSaveResult(1, 1);
    }


    /**
     * @param Translation[] $translations
     * @throws Exception
     */
    private function writeResxFile(string $filename, array $translations): int
    {
        $totalCount = 0;

        $xmlContent = (string)file_get_contents($filename);

        $xml = new SimpleXMLElement($xmlContent);
        $xml->registerXPathNamespace('x', 'http://schemas.microsoft.com/winfx/2006/xaml');

        foreach ($translations as $translation) {
            $key = $translation->getKey();
            $value = htmlspecialchars($translation->getValue(), ENT_XML1, 'UTF-8');

            // Check if the key already exists
            $existingElement = $xml->xpath("//x:data[@name='$key']");

            if ($existingElement) {
                $existingElement[0]->value = $value;
            } else {
                $dataElement = $xml->addChild('data');
                $dataElement->addAttribute('name', $key);
                $dataElement->addChild('value', $value);
            }

            $totalCount++;
        }

        $xml->asXML($filename);

        return $totalCount;
    }

    public function getContentFileTemplate(): string
    {
        return file_get_contents(__DIR__ . "/Template/StorageFileTemplate.resx") ?: '';
    }
}
