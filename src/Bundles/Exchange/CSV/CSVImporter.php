<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Bundles\Exchange\ImportInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageSaverInterface;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVImporter implements ImportInterface
{

    /**
     * @var StorageSaverInterface
     */
    private $saver;

    /**
     * @var string
     */
    private $delimiter;


    /**
     * @param StorageSaverInterface $saver
     * @param string $delimiter
     */
    public function __construct(StorageSaverInterface $saver, string $delimiter)
    {
        $this->saver = $saver;
        $this->delimiter = $delimiter;
    }


    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     * @throws \Exception
     */
    public function import(TranslationSet $set, string $filename): ImportResult
    {
        # first import our translations from our CSV file
        # into our TranslationSet
        $set = $this->importTranslations($set, $filename);

        # now save the set with the new values
        $result = $this->saver->saveTranslations($set);

        return new ImportResult(
            $result->getSavedLocales(),
            $result->getSavedTranslations()
        );
    }

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return TranslationSet
     * @throws \Exception
     */
    private function importTranslations(TranslationSet $set, string $filename): TranslationSet
    {
        $translationFileValues = [];
        $headerFiles = [];


        $csvFile = fopen($filename, 'r');

        if ($csvFile === false) {
            throw new \Exception('Error when opening CSV file: ' . $filename);
        }

        while ($row = fgetcsv($csvFile, 0, $this->delimiter)) {

            if (count($headerFiles) === 0) {
                # header line
                $headerFiles = $row;
            } else {

                for ($i = 1; $i <= count($row) - 1; $i++) {
                    $key = $row[0];
                    $value = $row[$i];

                    $transFile = (string)$headerFiles[$i];

                    if ($transFile !== '') {
                        $translationFileValues[$transFile][$key] = $value;
                    }
                }
            }
        }

        fclose($csvFile);


        foreach ($translationFileValues as $fileName => $csvTranslations) {

            $translationsForLocale = [];

            # search filename form locales
            foreach ($set->getLocales() as $locale) {
                $localeFileName = basename($locale->getFilename());

                if ($localeFileName !== $fileName) {
                    continue;
                }

                # create translations
                foreach ($csvTranslations as $csvTranslationKey => $csvTranslationValue) {
                    $translationsForLocale[] = new Translation($locale->getLocale(), $csvTranslationKey, (string)$csvTranslationValue);
                }

                $locale->setTranslations($translationsForLocale);
            }
        }

        return $set;
    }

}
