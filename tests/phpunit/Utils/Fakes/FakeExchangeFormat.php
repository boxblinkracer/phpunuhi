<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

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
        return [];
    }

    public function setOptionValues(array $options): void
    {
    }

    public function export(TranslationSet $set, string $outputDir, bool $onlyEmpty): void
    {
    }

    public function import(string $filename): ImportResult
    {
        return new ImportResult([]);
    }
}
