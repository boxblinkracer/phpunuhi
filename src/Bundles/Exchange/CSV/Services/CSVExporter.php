<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExporter
{

    /**
     * @var CSVWriterInterface
     */
    private $csvWriter;

    /**
     * @var string
     */
    private $delimiter;


    /**
     * @param CSVWriterInterface $csvWriter
     * @param string $delimiter
     */
    public function __construct(CSVWriterInterface $csvWriter, string $delimiter)
    {
        $this->csvWriter = $csvWriter;
        $this->delimiter = $delimiter;
    }


    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @param bool $onlyEmpty
     * @throws TranslationNotFoundException
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
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

        foreach ($set->getAllTranslationIDs() as $id) {
            $keyRow = [];

            if ($onlyEmpty) {
                $isComplete = $set->isCompletelyTranslated($id);

                # if it's already complete, do not export
                if ($isComplete) {
                    continue;
                }
            }

            # build our key entry with just the name
            # so based on our unique key, weg fetch any translation
            # and add the readable name
            foreach ($set->getLocales() as $locale) {
                $trans = $locale->findTranslation($id);

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
                        $trans = $locale->findTranslation($id);

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

        if ($outputDir === '' || $outputDir === '0') {
            $outputDir = '.';
        }

        $this->csvWriter->prepareDirectory($outputDir);

        $csvFilename = $outputDir . '/' . $set->getName() . '.csv';

        if (file_exists($csvFilename)) {
            $this->csvWriter->deleteFile($csvFilename);
        }

        $f = $this->csvWriter->open($csvFilename);

        if ($f !== null) {
            foreach ($csvExportLines as $row) {
                $this->csvWriter->writeLine($f, $row, $this->delimiter);
            }
            $this->csvWriter->close($f);
        }

        echo '   [+] generated file: ' . $csvFilename . PHP_EOL . PHP_EOL;
    }
}
