<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\JUnit;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\ReporterInterface;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;
use PHPUnuhi\Services\Writers\Xml\XmlWriterInterface;

class JUnitReporter implements ReporterInterface
{
    private DirectoryWriterInterface $directoryWriter;

    private XmlWriterInterface $xmlWriter;



    public function __construct(DirectoryWriterInterface $directoryWriter, XmlWriterInterface $xmlWriter)
    {
        $this->directoryWriter = $directoryWriter;
        $this->xmlWriter = $xmlWriter;
    }



    public function getName(): string
    {
        return 'junit';
    }


    public function getDefaultFilename(): string
    {
        return 'junit.xml';
    }


    public function generate(string $filename, ReportResult $report): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';

        $content .= '<testsuites name="PHPUnuhi Translation-Sets" tests="' . $report->getTestCount() . '" failures="' . $report->getFailureCount() . '">';


        foreach ($report->getSuites() as $suite) {
            $content .= '<testsuite name="' . $suite->getName() . '" tests="' . $suite->getTestCount() . '" failures="' . $suite->getFailureCount() . '">';

            foreach ($suite->getTests() as $test) {
                $content .= '<testcase name="' . $test->getName() . '" classname="' . $test->getClassName() . '">';

                if (!$test->isSuccess()) {
                    $lineNumber = '';
                    if (!empty($test->getLineNumber())) {
                        $lineNumber = '. Line ' . $test->getLineNumber();
                    }
                    $content .= '<failure type="' . $test->getFailureType() . '" message="' . $test->getFailureMessage() . $lineNumber . '"></failure>';
                }

                $content .= '</testcase>';
            }

            $content .= '</testsuite>';
        }

        $content .= '</testsuites>';

        $path = dirname($filename);

        if (!is_dir($path)) {
            $this->directoryWriter->createDirectory($path);
        }

        $this->xmlWriter->saveXml($filename, $content);
    }
}
