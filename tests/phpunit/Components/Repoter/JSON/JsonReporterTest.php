<?php

namespace phpunit\Components\Repoter\JSON;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeDirectoryWriter;
use phpunit\Utils\Fakes\FakeFileWriter;
use phpunit\Utils\Traits\StringCleanerTrait;
use phpunit\Utils\Traits\TestReportBuilderTrait;
use PHPUnuhi\Components\Reporter\JSON\JsonReporter;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;

class JsonReporterTest extends TestCase
{
    use TestReportBuilderTrait;
    use StringCleanerTrait;

    /**
     * @var JsonReporter
     */
    private $reporter;

    /**
     * @var FakeFileWriter
     */
    private $fakeFileWriter;

    /**
     * @var FakeDirectoryWriter
     */
    private $fakeDirectoryWriter;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->fakeDirectoryWriter = new FakeDirectoryWriter();
        $this->fakeFileWriter = new FakeFileWriter();

        $this->reporter = new JsonReporter($this->fakeDirectoryWriter, $this->fakeFileWriter);
    }


    /**
     * @return void
     */
    public function testName(): void
    {
        $this->assertEquals('json', $this->reporter->getName());
    }

    /**
     * @return void
     */
    public function testDefaultFilename(): void
    {
        $this->assertEquals('report.json', $this->reporter->getDefaultFilename());
    }

    /**
     * @return void
     */
    public function testCorrectFilenameIsWritten(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('my-file.json', $result);

        $this->assertEquals('my-file.json', $this->fakeFileWriter->getProvidedFilename());
    }

    /**
     * @return void
     */
    public function testSubfoldersAreGeneratedForResultFile(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('./subfolder/subfolder2/my-file.json', $result);

        $this->assertEquals('./subfolder/subfolder2', $this->fakeDirectoryWriter->getCreatedDirectory());
    }

    /**
     * @return void
     */
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
