<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVImporter
{

    /**
     * @var string
     */
    private $delimiter;


    /**
     * @param string $delimiter
     */
    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }


    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return void
     * @throws \Exception
     */
    public function import(TranslationSet $set, string $filename): void
    {
        # first import our translations from our CSV file
        # into our TranslationSet
        $this->importTranslations($set, $filename);
    }

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return void
     * @throws \Exception
     */
    private function importTranslations(TranslationSet $set, string $filename): void
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


        foreach ($translationFileValues as $identifier => $csvTranslations) {

            $translationsForLocale = [];

            # search filename form locales
            foreach ($set->getLocales() as $locale) {

                if ($locale->getExchangeIdentifier() !== $identifier) {
                    continue;
                }

                # create translations
                foreach ($csvTranslations as $csvTranslationKey => $csvTranslationValue) {
                    $translationsForLocale[] = new Translation($csvTranslationKey, (string)$csvTranslationValue, '');
                }

                $locale->setTranslations($translationsForLocale);
            }
        }
    }

}
