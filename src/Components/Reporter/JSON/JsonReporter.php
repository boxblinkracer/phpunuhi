<?php

namespace PHPUnuhi\Components\Reporter\JSON;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\SuiteResult;
use PHPUnuhi\Components\Reporter\ReporterInterface;

class JsonReporter implements ReporterInterface
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'json';
    }

    /**
     * @return string
     */
    public function getDefaultFilename(): string
    {
        return 'report.json';
    }

    /**
     * @param string $filename
     * @param ReportResult $report
     * @return void
     */
    public function generate(string $filename, ReportResult $report): void
    {
        $content = [
            'suites' => [],
        ];

        /** @var SuiteResult $suite */
        foreach ($report->getSuites() as $suite) {

            $suiteJson = [
                'name' => $suite->getName(),
                'tests' => $suite->getTestCount(),
                'failures' => $suite->getFailureCount(),
                'testCases' => [],
            ];

            foreach ($suite->getTests() as $test) {

                $suiteJson['testCases'][] = [
                    'name' => $test->getName(),
                    'className' => $test->getClassName(),
                    'success' => $test->isSuccess(),
                    'failureType' => $test->getFailureType(),
                    'failureMessage' => $test->getFailureMessage(),
                ];
            }

            $content['suites'][] = $suiteJson;
        }

        $path = dirname($filename);

        if (!is_dir($path)) {
            mkdir($path);
        }

        $json = json_encode($content, JSON_PRETTY_PRINT);

        file_put_contents($filename, $json);
    }

}
