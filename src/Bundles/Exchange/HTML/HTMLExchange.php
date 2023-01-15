<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\HTML\Services\HTMLExporter;
use PHPUnuhi\Bundles\Exchange\HTML\Services\HTMLImporter;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

class HTMLExchange implements ExchangeInterface
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'html';
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
        ];
    }

    /**
     * @param array<mixed> $options
     * @return void
     */
    public function setOptionValues(array $options): void
    {
    }

    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir): void
    {
        $exporter = new HTMLExporter();
        $exporter->export($set, $outputDir);
    }

    /**
     * @param string $filename
     * @return ImportResult
     * @throws \Exception
     */
    public function import(string $filename): ImportResult
    {
        $importer = new HTMLImporter();
        return $importer->import($filename);
    }

}