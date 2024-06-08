<?php

namespace PHPUnuhi\Tests\Utils\Traits;

use PHPUnuhi\Components\Reporter\Model\ReportTestResult;

trait TestReportBuilderTrait
{

    /**
     * @param bool $success
     * @return ReportTestResult
     */
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
