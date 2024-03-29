<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

use Exception;

class CSVWriter implements CSVWriterInterface
{


    /**
     * @param string $outputDir
     * @return void
     */
    public function prepareDirectory(string $outputDir): void
    {
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0775, true);
        }
    }

    /**
     * @param string $filename
     * @return void
     */
    public function deleteFile(string $filename): void
    {
        unlink($filename);
    }

    /**
     * @param string $filename
     * @throws Exception
     * @return null|resource
     */
    public function open(string $filename)
    {
        $resource = fopen($filename, 'ab');

        if (!$resource) {
            return null;
        }

        return $resource;
    }

    /**
     * @param $file
     * @param array<mixed> $row
     * @param string $delimiter
     * @return void
     */
    public function writeLine($file, array $row, string $delimiter): void
    {
        fputcsv($file, $row, $delimiter);
    }

    /**
     * @param $file
     * @return void
     */
    public function close($file): void
    {
        fclose($file);
    }
}
