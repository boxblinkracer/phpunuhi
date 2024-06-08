<?php

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriterInterface;

class FakeCSVWriter implements CSVWriterInterface
{

    /**
     * @var array<mixed>
     */
    private $writtenLines = [];


    /**
     * @param string $outputDir
     * @return void
     */
    public function prepareDirectory(string $outputDir): void
    {
    }

    /**
     * @return array<mixed>
     */
    public function getWrittenLines(): array
    {
        return $this->writtenLines;
    }

    /**
     * @param string $filename
     * @return void
     */
    public function deleteFile(string $filename): void
    {
    }

    /**
     * @param string $filename
     * @return null|resource
     */
    public function open(string $filename)
    {
        $file = fopen('data://text/plain;base64,' . base64_encode('my-memory-text'), 'r');

        if ($file === false) {
            return null;
        }

        return $file;
    }

    /**
     * @param resource $file
     * @param array<mixed> $row
     * @param string $delimiter
     * @return void
     */
    public function writeLine($file, array $row, string $delimiter): void
    {
        $this->writtenLines[] = $row;
    }

    /**
     * @param mixed $file
     * @return void
     */
    public function close($file): void
    {
    }
}
