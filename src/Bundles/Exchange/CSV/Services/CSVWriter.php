<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

class CSVWriter implements CSVWriterInterface
{


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
     * @return resource|void
     */
    public function open(string $filename)
    {
        return fopen($filename, 'ab');
    }

    /**
     * @param $file
     * @param array $row
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