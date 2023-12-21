<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ExchangeInterface
{

    /**
     * Gets the key name that is used to
     * identify this service
     * @return string
     */
    public function getName(): string;

    /**
     * Gets a list of available CLI options
     * whenever this service is used in a command
     * @return CommandOption[]
     */
    public function getOptions(): array;

    /**
     * Sets the CLI options for this service.
     * Please assign all API keys and other configurations in here.
     * @param array<mixed> $options
     * @return void
     */
    public function setOptionValues(array $options): void;

    /**
     * @param TranslationSet $set
     * @param string $outputDir
     * @param bool $onlyEmpty
     * @return void
     */
    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void;

    /**
     * @param string $filename
     * @return ImportResult
     */
    public function import(string $filename): ImportResult;

}