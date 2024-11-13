<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

interface CSVWriterInterface
{
    public function prepareDirectory(string $outputDir): void;


    public function deleteFile(string $filename): void;

    /**
     * @return null|resource
     */
    public function open(string $filename);

    /**
     * @param resource $file
     * @param array<mixed> $row
     */
    public function writeLine($file, array $row, string $delimiter): void;

    /**
     * @param resource $file
     */
    public function close($file): void;
}
