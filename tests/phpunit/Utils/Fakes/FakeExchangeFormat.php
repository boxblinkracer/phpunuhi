<?php

namespace phpunit\Utils\Fakes;

use PHPUnuhi\Bundles\Exchange\ExchangeInterface;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class FakeExchangeFormat implements ExchangeInterface
{

    public function getName(): string
    {
       return 'fake';
    }

    public function getOptions(): array
    {
        // TODO: Implement getOptions() method.
    }

    public function setOptionValues(array $options): void
    {
        // TODO: Implement setOptionValues() method.
    }

    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
        // TODO: Implement export() method.
    }

    public function import(string $filename): ImportResult
    {
        // TODO: Implement import() method.
    }


}