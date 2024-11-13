<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\Service;

use PHPUnuhi\Components\Reporter\Model\ReportTestResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;

class ReportTestResultConverter
{
    public function toTestResult(ValidationTest $test): ReportTestResult
    {
        return new ReportTestResult(
            $test->getTitle(),
            $test->getTranslationKey(),
            basename($test->getFilename()),
            $test->getLineNumber(),
            $test->getClassification(),
            $test->getFailureMessage(),
            $test->isSuccess()
        );
    }
}
