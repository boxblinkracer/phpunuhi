<?php

namespace PHPUnuhi\Bundles\Storage\CSV;

use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriterInterface;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVStorage implements StorageInterface
{

    /**
     * @var CSVWriterInterface
     */
    private $csvWriter;


    /**
     * @param CSVWriterInterface $csvWriter
     */
    public function __construct(CSVWriterInterface $csvWriter)
    {
        $this->csvWriter = $csvWriter;
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
        return new StorageHierarchy(false, '');
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslations(TranslationSet $set): void
    {
        $filename = '';

        $csvLines = [];

        $csvFile = fopen($filename, 'r');

        while ($row = fgetcsv($csvFile, 0, ';')) {
            $csvLines[] = $row;
        }
        fclose($csvFile);

        foreach ($csvLines as $line) {

            $rowKey = $line[0];
            $column = 1;

            foreach ($set->getLocales() as $locale) {

                $langValue = $line[$column];

                $locale->addTranslation($rowKey, $langValue, '');

                $column++;
            }
        }
    }


    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $outputDir = '';
        $csvExportLines = [];

        $countLocales = count($set->getLocales());
        $countTranslations = 0;

        foreach ($set->getAllTranslationIDs() as $key) {

            $keyRow = [];

            foreach ($set->getLocales() as $locale) {

                try {

                    # search for our translation
                    # and add the value if found
                    $trans = $locale->findTranslation($key);

                    $keyRow[] = $trans->getValue();

                } catch (TranslationNotFoundException $ex) {
                    # if we have no translation, add an empty value
                    $keyRow[] = '';
                }

                $countTranslations++;
            }

            # append to CSV lines
            $csvExportLines[] = $keyRow;
        }

        # ----------------------------------------------------------------------------------------
        # WRITE CSV lines

        if (empty($outputDir)) {
            $outputDir = '.';
        }

        $this->csvWriter->prepareDirectory($outputDir);

        $csvFilename = $outputDir . '/' . $set->getName() . '.csv';

        if (file_exists($csvFilename)) {
            $this->csvWriter->deleteFile($csvFilename);
        }

        $f = $this->csvWriter->open($csvFilename);

        if ($f !== false) {
            foreach ($csvExportLines as $row) {
                $this->csvWriter->writeLine($f, $row, ';');
            }
            $this->csvWriter->close($f);
        }

        echo '   [+] generated file: ' . $csvFilename . PHP_EOL . PHP_EOL;

        return new StorageSaveResult(
            $countLocales,
            $countTranslations
        );
    }

}