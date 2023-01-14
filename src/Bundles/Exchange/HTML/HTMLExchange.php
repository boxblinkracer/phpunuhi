<?php

namespace PHPUnuhi\Bundles\Exchange\HTML;

use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

class HTMLExchange implements ExchangeInterface
{

    /**
     * @var StorageInterface
     */
    private $storage;


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
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     * @throws \Exception
     */
    public function import(TranslationSet $set, string $filename): ImportResult
    {
        $importer = new HTMLImporter($this->storage);

        return $importer->import($set, $filename);
    }

}