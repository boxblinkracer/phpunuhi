<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ExchangeInterface
{

    /**
     * Gets the key name that is used to
     * identify this service
     * @return string
     */
    function getName(): string;

    /**
     * Gets a list of available CLI options
     * whenever this service is used in a command
     * @return CommandOption[]
     */
    function getOptions(): array;

    /**
     * Sets the used Storage object for the exchange
     * @param StorageInterface $storage
     * @return void
     */
    function setStorage(StorageInterface $storage): void;

    /**
     * Sets the CLI options for this service.
     * Please assign all API keys and other configurations in here.
     * @param array<mixed> $options
     * @return void
     */
    function setOptionValues(array $options) : void;

    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @return void
     */
    function export(TranslationSet $set, string $outputDir): void;

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     */
    function import(TranslationSet $set, string $filename): ImportResult;

}