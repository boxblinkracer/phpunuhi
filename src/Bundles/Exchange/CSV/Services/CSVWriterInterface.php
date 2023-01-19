<?php

namespace PHPUnuhi\Bundles\Exchange\CSV\Services;

interface CSVWriterInterface
{

    /**
     * @param string $filename
     * @return void
     */
    function deleteFile(string $filename): void;

    /**
     * @param string $filename
     * @return resource
     */
    function open(string $filename);

    /**
     * @param resource $file
     * @param array<mixed> $row
     * @param string $delimiter
     * @return void
     */
    function writeLine($file, array $row, string $delimiter): void;

    /**
     * @param resource $file
     * @return void
     */
    function close($file): void;

}