<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

interface CSVWriterInterface
{

    /**
     * @param string $outputDir
     * @return void
     */
    public function prepareDirectory(string $outputDir): void;

    /**
     * @param string $filename
     * @return void
     */
    public function deleteFile(string $filename): void;

    /**
     * @param string $filename
     * @return resource
     */
    public function open(string $filename) : mixed;

    /**
     * @param resource $file
     * @param array<mixed> $row
     * @param string $delimiter
     * @return void
     */
    public function writeLine($file, array $row, string $delimiter): void;

    /**
     * @param resource $file
     * @return void
     */
    public function close($file): void;
}
