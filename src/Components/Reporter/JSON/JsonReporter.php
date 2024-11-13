<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\JSON;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\ReporterInterface;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;
use PHPUnuhi\Services\Writers\File\FileWriterInterface;

class JsonReporter implements ReporterInterface
{
    private DirectoryWriterInterface $directoryWriter;

    private FileWriterInterface $fileWriter;



    public function __construct(DirectoryWriterInterface $directoryWriter, FileWriterInterface $fileWriter)
    {
        $this->directoryWriter = $directoryWriter;
        $this->fileWriter = $fileWriter;
    }



    public function getName(): string
    {
        return 'json';
    }


    public function getDefaultFilename(): string
    {
        return 'report.json';
    }


    public function generate(string $filename, ReportResult $report): void
    {
        $content = [
            'suites' => [],
        ];

        foreach ($report->getSuites() as $suite) {
            $suiteJson = [
                'name' => $suite->getName(),
                'tests' => $suite->getTestCount(),
                'failures' => $suite->getFailureCount(),
                'testCases' => [],
            ];

            foreach ($suite->getTests() as $test) {

                /**
                 * {
                 * "name": "[de] Text structure of key: app.test",
                 * "key": "app.test",
                 * "location": "admin.de.yaml",
                 * "success": true,
                 * "failureType": "STRUCTURE",
                 * "failureMessage": ""
                 * }
                 */
                $suiteJson['testCases'][] = [
                    'name' => $test->getName(),
                    'key' => $test->getTranslationKey(),
                    'location' => $test->getClassName(),
                    'lineNumber' => $test->getLineNumber(),
                    'success' => $test->isSuccess(),
                    'failureType' => $test->getFailureType(),
                    'failureMessage' => $test->getFailureMessage(),
                ];
            }

            $content['suites'][] = $suiteJson;
        }

        $path = dirname($filename);

        if (!is_dir($path)) {
            $this->directoryWriter->createDirectory($path);
        }

        $json = json_encode($content, JSON_PRETTY_PRINT);

        $this->fileWriter->writeFile($filename, (string)$json);
    }
}
