<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class CSVImporter
{

    use StringTrait;


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

                $startIndex = 1;

                if (in_array('Group', $headerFiles)) {
                    $group = 'group--' . $row[0] . '.';
                    $key = $row[1];
                    $startIndex = 2;
                } else {
                    $key = $row[0];
                    $group = '';
                }

                for ($i = $startIndex; $i <= count($row) - 1; $i++) {

                    $value = $row[$i];

                    $transFile = (string)$headerFiles[$i];

                    if ($transFile !== '') {
                        $transKey = $group . $key;
                        $translationFileValues[$transFile][$transKey] = $value;
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

                    $group = '';
                    if ($this->stringStartsWith($csvTranslationKey, 'group--')) {
                        $group = explode('.', $csvTranslationKey)[0];
                        $group = str_replace('group--', '', $group);

                        $csvTranslationKey = str_replace('group--' . $group . '.', '', $csvTranslationKey);
                    }

                    $translationsForLocale[] = new Translation($csvTranslationKey, (string)$csvTranslationValue, $group);
                }

                $locale->setTranslations($translationsForLocale);
            }
        }
    }

}
