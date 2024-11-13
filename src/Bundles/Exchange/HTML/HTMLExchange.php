<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange\HTML;

use Exception;
use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\HTML\Services\HTMLExporter;
use PHPUnuhi\Bundles\Exchange\HTML\Services\HTMLImporter;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

class HTMLExchange implements ExchangeInterface
{
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
     */
    public function setOptionValues(array $options): void
    {
    }

    /**
     * @throws TranslationNotFoundException
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
        $exporter = new HTMLExporter();
        $exporter->export($set, $outputDir, $onlyEmpty);
    }

    /**
     * @throws Exception
     */
    public function import(string $filename): ImportResult
    {
        $importer = new HTMLImporter();
        return $importer->import($filename);
    }
}
