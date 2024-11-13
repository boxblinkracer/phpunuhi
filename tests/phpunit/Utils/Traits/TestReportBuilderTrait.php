<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Traits;

use PHPUnuhi\Components\Reporter\Model\ReportTestResult;

trait TestReportBuilderTrait
{
    protected function buildTestResult(bool $success): ReportTestResult
    {
        return new ReportTestResult(
            'Fake Test',
            'btnTitle',
            'CONTENT_VALIDATOR',
            2,
            'MISSING_VALUE',
            'The value is missing',
            $success
        );
    }
}
