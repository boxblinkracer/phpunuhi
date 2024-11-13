<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Translation\TranslationSet;

interface ExchangeInterface
{
    /**
     * Gets the key name that is used to
     * identify this service
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
     */
    public function setOptionValues(array $options): void;


    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void;


    public function import(string $filename): ImportResult;
}
