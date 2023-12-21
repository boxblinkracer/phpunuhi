<?php

namespace phpunit\Utils\Traits;

use PHPUnuhi\Components\Reporter\Model\TestResult;

trait TestReportBuilderTrait
{

    /**
     * @param bool $success
     * @return TestResult
     */
    protected function buildTestResult(bool $success): TestResult
    {
        return new TestResult(
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