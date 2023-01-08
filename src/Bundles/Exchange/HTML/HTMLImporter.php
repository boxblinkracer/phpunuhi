<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ImportInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use SplFileObject;

class HTMLImporter implements ImportInterface
{

    /**
     * @var StorageInterface
     */
    private $storage;


    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }


    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     */
    function import(TranslationSet $set, string $filename): ImportResult
    {

        $foundData = [];

        foreach (new SplFileObject($filename) as $line) {

            if ($line === false) {
                $line = '';
            }

            $line = str_replace(PHP_EOL, '', $line);

            if (is_array($line)) {
                $line = '';
            }

            if (trim($line) === '') {
                continue;
            }

            $keyLocale = explode('=', $line)[0];

            $key = explode('--', $keyLocale)[0];
            $localeID = explode('--', $keyLocale)[1];
            $value = explode('=', $line)[1];

            $foundData[] = [
                'key' => $key,
                'locale' => $localeID,
                'value' => $value,
            ];

        }

        foreach ($set->getLocales() as $locale) {

            $newTranslations = [];

            foreach ($foundData as $data) {
                if ($data['locale'] === $locale->getExchangeIdentifier()) {
                    $newTranslations[] = new Translation($data['key'], $data['value']);
                }
            }

            $locale->setTranslations($newTranslations);
        }


        $result = $this->storage->saveTranslations($set);

        return new ImportResult($result->getSavedLocales(), $result->getSavedTranslations());
    }


}