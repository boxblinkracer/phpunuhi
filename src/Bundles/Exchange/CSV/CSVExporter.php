<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExporter
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
     * @param string $outputDir
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir): void
    {
        $csvExportLines = [];

        # ----------------------------------------------------------------------------------------

        $sortedLanguagesColumns = [];

        # collect available languages
        # in the correct sorting of the columns
        foreach ($set->getLocales() as $locale) {
            $sortedLanguagesColumns[] = $locale->getExchangeIdentifier();
        }

        # ----------------------------------------------------------------------------------------
        # BUILD HEADER LINE

        $headerLine = [];

        if ($set->hasGroups()) {
            $headerLine[] = 'Group';
        }

        $headerLine[] = 'Key';

        foreach ($sortedLanguagesColumns as $col) {
            $headerLine[] = $col;
        }

        $csvExportLines[] = $headerLine;

        # ----------------------------------------------------------------------------------------
        # BUILD DATA LINES

        foreach ($set->getAllTranslationEntryIDs() as $key) {

            $keyRow = [];

            # build our key entry with just the name
            # so based on our unique key, weg fetch any translation
            # and add the readable name
            foreach ($set->getLocales() as $locale) {
                $trans = $locale->findTranslation($key);

                if (!empty($trans->getGroup())) {
                    $keyRow[] = $trans->getGroup();
                }

                $keyRow[] = $trans->getKey();
                break;
            }

            # use the same sorting as our header line
            foreach ($sortedLanguagesColumns as $colName) {

                foreach ($set->getLocales() as $locale) {

                    # only use the one from our current column
                    if ($locale->getExchangeIdentifier() !== $colName) {
                        continue;
                    }

                    try {

                        # search for our translation
                        # and add the value if found
                        $trans = $locale->findTranslation($key);

                        $keyRow[] = $trans->getValue();

                    } catch (TranslationNotFoundException $ex) {
                        # if we have no translation, add an empty value
                        $keyRow[] = '';
                    }
                }
            }

            # append to CSV lines
            $csvExportLines[] = $keyRow;
        }

        # ----------------------------------------------------------------------------------------
        # WRITE CSV lines

        if (empty($outputDir)) {
            $outputDir = '.';
        }

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $csvFilename = $outputDir . '/' . $set->getName() . '.csv';

        if (file_exists($csvFilename)) {
            unlink($csvFilename);
        }

        $f = fopen($csvFilename, 'ab');

        if ($f !== false) {
            foreach ($csvExportLines as $row) {
                fputcsv($f, $row, $this->delimiter);
            }
            fclose($f);
        }

        echo '   [+] generated file: ' . $csvFilename . PHP_EOL . PHP_EOL;

    }

}
