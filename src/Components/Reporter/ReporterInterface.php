<?php

namespace PHPUnuhi\Components\Reporter;

use PHPUnuhi\Components\Reporter\Model\ReportResult;

interface ReporterInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDefaultFilename(): string;

    /**
     * @param string $filename
     * @param ReportResult $report
     * @return void
     */
    public function generate(string $filename, ReportResult $report): void;

}