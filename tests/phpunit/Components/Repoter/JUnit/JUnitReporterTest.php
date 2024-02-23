<?php

namespace phpunit\Components\Repoter\JUnit;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeDirectoryWriter;
use phpunit\Utils\Fakes\FakeXmlWriter;
use phpunit\Utils\Traits\StringCleanerTrait;
use phpunit\Utils\Traits\TestReportBuilderTrait;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;

class JUnitReporterTest extends TestCase
{
    use TestReportBuilderTrait;
    use StringCleanerTrait;


    /**
     * @var JUnitReporter
     */
    private $reporter;

    /**
     * @var FakeXmlWriter
     */
    private $fakeXmlWriter;

    /**
     * @var FakeDirectoryWriter
     */
    private $fakeDirectoryWriter;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->fakeXmlWriter = new FakeXmlWriter();
        $this->fakeDirectoryWriter = new FakeDirectoryWriter();

        $this->reporter = new JUnitReporter(
            $this->fakeDirectoryWriter,
            $this->fakeXmlWriter
        );
    }


    /**
     * @return void
     */
    public function testName(): void
    {
        $this->assertEquals('junit', $this->reporter->getName());
    }

    /**
     * @return void
     */
    public function testDefaultFilename(): void
    {
        $this->assertEquals('junit.xml', $this->reporter->getDefaultFilename());
    }

    /**
     * @return void
     */
    public function testCorrectFilenameIsWritten(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('my-file.xml', $result);

        $this->assertEquals('my-file.xml', $this->fakeXmlWriter->getProvidedFilename());
    }

    /**
     * @return void
     */
    public function testSubfoldersAreGeneratedForResultFile(): void
    {
        $result = new ReportResult();

        $this->reporter->generate('./subfolder/subfolder2/my-file.xml', $result);

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


        $this->reporter->generate('.my-file.xml', $result);


        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<testsuites name="PHPUnuhi Translation-Sets" tests="2" failures="1">
    <testsuite name="Storefront" tests="2" failures="1">
        <testcase name="FakeTest" classname="CONTENT_VALIDATOR">
        </testcase>
        <testcase name="FakeTest" classname="CONTENT_VALIDATOR">
            <failure type="MISSING_VALUE" message="The value is missing . Line 2"></failure>
        </testcase>
    </testsuite>
</testsuites>
XML;

        $actual = $this->fakeXmlWriter->getProvidedXml();

        $expected = $this->buildComparableString($expected);
        $actual = $this->buildComparableString($actual);

        $this->assertEquals($expected, $actual);
    }
}
