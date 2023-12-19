<?php

namespace phpunit\Fakes;

use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriterInterface;

class FakeCSVWriter implements CSVWriterInterface
{

    /**
     * @var array<mixed>
     */
    private $writtenLines = [];


    /**
     *
     */
    public function __construct()
    {
    }


    /**
     * @param string $outputDir
     * @return void
     */
    public function prepareDirectory(string $outputDir): void
    {
    }

    /**
     * @return array|string[]
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
        // TODO: Implement deleteFile() method.
    }

    /**
     * @param string $filename
     * @return resource|void
     */
    public function open(string $filename)
    {
        // TODO: Implement open() method.
    }

    /**
     * @param $file
     * @param array $row
     * @param string $delimiter
     * @return void
     */
    public function writeLine($file, array $row, string $delimiter): void
    {
        $this->writtenLines[] = $row;
    }

    /**
     * @param $file
     * @return void
     */
    public function close($file): void
    {
        // TODO: Implement close() method.
    }

}