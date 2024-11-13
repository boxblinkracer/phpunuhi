<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange\CSV;

use Exception;
use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVExporter;
use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVImporter;
use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriterInterface;
use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

class CSVExchange implements ExchangeInterface
{
    private CSVWriterInterface $csvWriter;

    private string $csvDelimiter = ',';



    public function __construct(CSVWriterInterface $csvWriter)
    {
        $this->csvWriter = $csvWriter;
    }



    public function getName(): string
    {
        return 'csv';
    }


    public function getCsvDelimiter(): string
    {
        return $this->csvDelimiter;
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
     * @param array<mixed> $options
     */
    public function setOptionValues(array $options): void
    {
        $this->csvDelimiter = isset($options['csv-delimiter']) ? (string)$options['csv-delimiter'] : '';

        $this->csvDelimiter = trim($this->csvDelimiter);

        if ($this->csvDelimiter === '') {
            $this->csvDelimiter = ',';
        }
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
        $exporter = new CSVExporter($this->csvWriter, $this->csvDelimiter);
        $exporter->export($set, $outputDir, $onlyEmpty);
    }

    /**
     * @throws Exception
     */
    public function import(string $filename): ImportResult
    {
        $importer = new CSVImporter($this->csvDelimiter);
        return $importer->import($filename);
    }
}
