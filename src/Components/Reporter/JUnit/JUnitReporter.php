<?php

namespace PHPUnuhi\Components\Reporter\JUnit;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\ReporterInterface;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;
use PHPUnuhi\Services\Writers\Xml\XmlWriterInterface;

class JUnitReporter implements ReporterInterface
{

    /**
     * @var DirectoryWriterInterface
     */
    private $directoryWriter;

    /**
     * @var XmlWriterInterface
     */
    private $xmlWriter;


    /**
     * @param DirectoryWriterInterface $directoryWriter
     * @param XmlWriterInterface $xmlWriter
     */
    public function __construct(DirectoryWriterInterface $directoryWriter, XmlWriterInterface $xmlWriter)
    {
        $this->directoryWriter = $directoryWriter;
        $this->xmlWriter = $xmlWriter;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'junit';
    }

    /**
     * @return string
     */
    public function getDefaultFilename(): string
    {
        return 'junit.xml';
    }

    /**
     * @param string $filename
     * @param ReportResult $report
     * @return void
     */
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
