<?php

namespace PHPUnuhi\Components\Reporter\JUnit;

use DOMDocument;
use PHPUnuhi\Components\Reporter\Model\ReportResult;

class JUnitReporter
{

    /**
     * @var string
     */
    private $filename;


    /**
     * Reports constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }


    /**
     * @param ReportResult $report
     * @return void
     */
    public function generate(ReportResult $report): void
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>';

        $content .= '<testsuites name="PHPUnuhi Translation-Sets" tests="' . $report->getTestCount() . '" failures="' . $report->getFailureCount() . '">';


        foreach ($report->getSuites() as $suite) {

            $content .= '<testsuite name="' . $suite->getName() . '" tests="' . $suite->getTestCount() . '" failures="' . $suite->getFailureCount() . '">';

            foreach ($suite->getTests() as $test) {

                $content .= '<testcase name="' . $test->getName() . '" classname="' . $test->getClassName() . '">';

                if (!$test->isSuccess()) {
                    $content .= '<failure type="' . $test->getFailureType() . '" message="Test is not successful"></failure>';
                }

                $content .= '</testcase>';
            }

            $content .= '</testsuite>';
        }

        $content .= '</testsuites>';

        $path = dirname($this->filename);

        if (!is_dir($path)) {
            mkdir($path);
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($content);
        $out = $dom->saveXML();

        file_put_contents($this->filename, $out);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

}
