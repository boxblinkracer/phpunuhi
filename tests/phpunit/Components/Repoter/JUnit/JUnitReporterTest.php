<?php

namespace phpunit\Components\Repoter\JUnit;

use phpunit\Fakes\FakeDirectoryWriter;
use phpunit\Fakes\FakeXmlWriter;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;


class JUnitReporterTest extends TestCase
{

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

}
