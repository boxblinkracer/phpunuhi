<?php

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

    /**
     * @var CSVWriterInterface
     */
    private $csvWriter;

    /**
     * @var string
     */
    private $csvDelimiter = ',';


    /**
     * @param CSVWriterInterface $csvWriter
     */
    public function __construct(CSVWriterInterface $csvWriter)
    {
        $this->csvWriter = $csvWriter;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'csv';
    }

    /**
     * @return string
     */
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
     * @return void
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
     * @param TranslationSet $set
     * @param string $outputDir
     * @param bool $onlyEmpty
     * @throws TranslationNotFoundException
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
        $exporter = new CSVExporter($this->csvWriter, $this->csvDelimiter);
        $exporter->export($set, $outputDir, $onlyEmpty);
    }

    /**
     * @param string $filename
     * @throws Exception
     * @return ImportResult
     */
    public function import(string $filename): ImportResult
    {
        $importer = new CSVImporter($this->csvDelimiter);
        return $importer->import($filename);
    }
}
