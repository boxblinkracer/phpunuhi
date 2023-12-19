<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

use PHPUnuhi\Bundles\Exchange\ImportEntry;
use PHPUnuhi\Bundles\Exchange\ImportResult;
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
     * @param string $filename
     * @return ImportResult
     * @throws \Exception
     */
    public function import(string $filename): ImportResult
    {
        $headerFiles = [];
        $importData = [];

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
                    $group = $row[0];
                    $key = $row[1];
                    $startIndex = 2;
                } else {
                    $key = $row[0];
                    $group = '';
                }

                for ($i = $startIndex; $i <= count($row) - 1; $i++) {

                    $value = $row[$i];

                    $localeExchangeID = (string)$headerFiles[$i];

                    if ($localeExchangeID !== '') {

                        $importData[] = new ImportEntry(
                            $localeExchangeID,
                            (string)$key,
                            (string)$group,
                            (string)$value
                        );
                    }
                }
            }
        }

        fclose($csvFile);

        return new ImportResult($importData);
    }

}
