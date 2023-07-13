<?php

namespace PHPUnuhi\Components\Reporter\JUnit;

use DOMDocument;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\ReporterInterface;


class JUnitReporter implements ReporterInterface
{

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
                    $content .= '<failure type="' . $test->getFailureType() . '" message="' . $test->getFailureMessage() . '"></failure>';
                }

                $content .= '</testcase>';
            }

            $content .= '</testsuite>';
        }

        $content .= '</testsuites>';

        $path = dirname($filename);

        if (!is_dir($path)) {
            mkdir($path);
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $dom->loadXML($content);
        $out = $dom->saveXML();

        file_put_contents($filename, $out);
    }


}
