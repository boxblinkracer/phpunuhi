<?php

namespace PHPUnuhi\Bundles\Exchange\CSV;

use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExchange implements ExchangeInterface
{

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $csvDelimiter;


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'csv';
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
            new CommandOption('csv-delimiter', true),
        ];
    }

    /**
     * @param StorageInterface $storage
     * @return void
     */
    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @param array<mixed> $options
     * @return void
     */
    public function setOptionValues(array $options): void
    {
        $this->csvDelimiter = (string)$options['csv-delimiter'];

        if (empty($this->csvDelimiter)) {
            $this->csvDelimiter = ',';
        }
    }

    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir): void
    {
        $exporter = new CSVExporter($this->csvDelimiter);
        $exporter->export($set, $outputDir);
    }

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     * @throws \Exception
     */
    public function import(TranslationSet $set, string $filename): ImportResult
    {
        $importer = new CSVImporter($this->storage, $this->csvDelimiter);

        return $importer->import($set, $filename);
    }

}