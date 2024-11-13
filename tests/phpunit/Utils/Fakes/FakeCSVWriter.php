<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriterInterface;

class FakeCSVWriter implements CSVWriterInterface
{
    /**
     * @var array<mixed>
     */
    private array $writtenLines = [];



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


    public function deleteFile(string $filename): void
    {
    }

    /**
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
     */
    public function writeLine($file, array $row, string $delimiter): void
    {
        $this->writtenLines[] = $row;
    }


    public function close($file): void
    {
    }
}
