<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Repoter\JSON;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\JSON\JsonReporter;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Tests\Utils\Fakes\FakeDirectoryWriter;
use PHPUnuhi\Tests\Utils\Fakes\FakeFileWriter;
use PHPUnuhi\Tests\Utils\Traits\StringCleanerTrait;
use PHPUnuhi\Tests\Utils\Traits\TestReportBuilderTrait;

class JsonReporterTest extends TestCase
{
    use TestReportBuilderTrait;
    use StringCleanerTrait;


    private JsonReporter $reporter;


    private FakeFileWriter $fakeFileWriter;


    private FakeDirectoryWriter $fakeDirectoryWriter;


    public function setUp(): void
    {
        $this->fakeDirectoryWriter = new FakeDirectoryWriter();
        $this->fakeFileWriter = new FakeFileWriter();

        $this->reporter = new JsonReporter($this->fakeDirectoryWriter, $this->fakeFileWriter);
    }



    public function testName(): void
    {
        $this->assertEquals('json', $this->reporter->getName());
    }


    public function testDefaultFilename(): void
    {
        $this->assertEquals('report.json', $this->reporter->getDefaultFilename());
    }


    public function testCorrectFilenameIsWritten(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('my-file.json', $result);

        $this->assertEquals('my-file.json', $this->fakeFileWriter->getProvidedFilename());
    }


    public function testSubfoldersAreGeneratedForResultFile(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('./subfolder/subfolder2/my-file.json', $result);

        $this->assertEquals('./subfolder/subfolder2', $this->fakeDirectoryWriter->getCreatedDirectory());
    }


    public function testReportGeneration(): void
    {
        $suite = new ReportSetResult('Storefront');
        $suite->addTestResult($this->buildTestResult(true));
        $suite->addTestResult($this->buildTestResult(false));

        $result = new ReportResult();
        $result->addTranslationSet($suite);

        $this->reporter->generate('my-report.json', $result);

        $expected = <<<JSON
{
   "suites": [
       {
           "name": "Storefront",
           "tests": 2,
           "failures": 1,
           "testCases": [
               {
                   "name": "Fake Test",
                   "key": "btnTitle",
                   "location": "CONTENT_VALIDATOR",
                   "lineNumber": 2,
                   "success": true,
                   "failureType": "MISSING_VALUE",
                   "failureMessage": "The value is missing"
               },
               {
                   "name": "Fake Test",
                   "key": "btnTitle",
                   "location": "CONTENT_VALIDATOR",
                   "lineNumber": 2,
                   "success": false,
                   "failureType": "MISSING_VALUE",
                   "failureMessage": "The value is missing"
               }
           ]
       }
   ]
}
JSON;


        $actual = $this->fakeFileWriter->getProvidedContent();

        $expected = $this->buildComparableString($expected);
        $actual = $this->buildComparableString($actual);

        $this->assertEquals($expected, $actual);
    }
}
