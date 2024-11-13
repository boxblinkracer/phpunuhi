<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter;

use PHPUnuhi\Components\Reporter\Model\ReportResult;

interface ReporterInterface
{
    public function getName(): string;


    public function getDefaultFilename(): string;


    public function generate(string $filename, ReportResult $report): void;
}
