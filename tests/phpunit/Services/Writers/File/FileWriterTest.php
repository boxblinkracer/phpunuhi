<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Writers\File;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Writers\File\FileWriter;

class FileWriterTest extends TestCase
{
    /**
     * @var string
     */
    protected $testFilePath;



    protected function setUp(): void
    {
        $this->testFilePath = __DIR__ . '/testfile.txt';
    }


    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
    }


    public function testWriteFile(): void
    {
        $fileWriter = new FileWriter();
        $content = "Test content";

        $fileWriter->writeFile($this->testFilePath, $content);

        $this->assertFileExists($this->testFilePath);
        $this->assertEquals($content, file_get_contents($this->testFilePath));
    }
}
