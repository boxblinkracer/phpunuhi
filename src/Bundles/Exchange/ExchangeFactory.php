<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Bundles\Exchange\CSV\CSVExporter;
use PHPUnuhi\Bundles\Exchange\CSV\CSVImporter;
use PHPUnuhi\Bundles\Exchange\HTML\HTMLExporter;
use PHPUnuhi\Bundles\Exchange\HTML\HTMLImporter;
use PHPUnuhi\Bundles\Storage\StorageSaverInterface;

class ExchangeFactory
{

    /**
     * @param string $format
     * @param StorageSaverInterface $translationSaver
     * @param string $delimiter
     * @return ImportInterface
     * @throws \Exception
     */
    public static function getImporterFromFormat(string $format, StorageSaverInterface $translationSaver, string $delimiter): ImportInterface
    {
        switch (strtolower($format)) {
            case ExchangeFormat::CSV:
                return new CSVImporter($translationSaver, $delimiter);

            case ExchangeFormat::HTML:
                return new HTMLImporter();

            default:
                throw new \Exception('No importer found for ExchangeFormat: ' . $format);
        }
    }

    /**
     * @param string $format
     * @param string $delimiter
     * @return ExportInterface
     * @throws \Exception
     */
    public static function getExporterFromFormat(string $format, string $delimiter): ExportInterface
    {
        switch (strtolower($format)) {
            case ExchangeFormat::CSV:
                return new CSVExporter($delimiter);

            case ExchangeFormat::HTML:
                return new HTMLExporter();

            default:
                throw new \Exception('No exporter found for ExchangeFormat: ' . $format);
        }
    }

}
