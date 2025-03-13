<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

use Exception;

class CSVWriter implements CSVWriterInterface
{
    public function prepareDirectory(string $outputDir): void
    {
        if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true) && !is_dir($outputDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputDir));
        }
    }


    public function deleteFile(string $filename): void
    {
        unlink($filename);
    }

    /**
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
     */
    public function writeLine($file, array $row, string $delimiter): void
    {
        fputcsv($file, $row, $delimiter);
    }

    /**
     * @param $file
     */
    public function close($file): void
    {
        fclose($file);
    }
}
