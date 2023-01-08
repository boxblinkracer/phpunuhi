<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Bundles\Exchange\ExportInterface;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExporter implements ExportInterface
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
        $allEntries = [];

        foreach ($set->getLocales() as $locale) {

            $fileBase = basename($locale->getFilename());

            foreach ($locale->getTranslations() as $translation) {
                $allEntries[$translation->getKey()][$fileBase] = $translation->getValue();
            }
        }


        $headerLine = [];
        $headerLine[] = 'Key';

        foreach ($set->getLocales() as $locale) {
            $headerLine[] = $locale->getExchangeIdentifier();
        }

        $lines = [];
        $lines[] = $headerLine;

        foreach ($allEntries as $key => $values) {

            $headerLine = [];
            $headerLine[] = $key;
            foreach ($values as $value) {
                $headerLine[] = $value;
            }

            $lines[] = $headerLine;
        }

        if (empty($outputDir)) {
            $outputDir = '.';
        }

        # required for phar
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $outputDir = $workingDir . '/' . $outputDir;


        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0775, true);
        }


        $csvFilename = $outputDir . '/' . $set->getName() . '.csv';


        if (file_exists($csvFilename)) {
            unlink($csvFilename);
        }

        $f = fopen($csvFilename, 'a');

        if ($f !== false) {
            foreach ($lines as $row) {
                fputcsv($f, $row, $this->delimiter);
            }
            fclose($f);
        }

        echo '   [+] generated file: ' . $csvFilename . PHP_EOL . PHP_EOL;

    }

}