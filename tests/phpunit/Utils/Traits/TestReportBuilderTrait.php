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
            '',
            '',
            '',
            2,
            '',
            '',
            $success
        );
    }

}