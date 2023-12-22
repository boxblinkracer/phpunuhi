<?php

namespace phpunit\Services\Writers\Directory;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriter;

class DirectoryWriterTest extends TestCase
{

    /**
     * @var string
     */
    protected $testDirectoryPath;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->testDirectoryPath = __DIR__ . '/test_directory';
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (is_dir($this->testDirectoryPath)) {
            $this->deleteDirectory($this->testDirectoryPath);
        }
    }


    /**
     * @return void
     */
    public function testCreateDirectory(): void
    {
        $directoryWriter = new DirectoryWriter();

        $directoryWriter->createDirectory($this->testDirectoryPath);

        $this->assertDirectoryExists($this->testDirectoryPath);
    }

    /**
     * @return void
     */
    public function testCreateDirectoryTwice(): void
    {
        $directoryWriter = new DirectoryWriter();

        $directoryWriter->createDirectory($this->testDirectoryPath);
        $directoryWriter->createDirectory($this->testDirectoryPath);

        $this->assertDirectoryExists($this->testDirectoryPath);
    }


    /**
     * @param $path
     * @return void
     */
    private function deleteDirectory($path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            is_dir("$path/$file") ? $this->deleteDirectory("$path/$file") : unlink("$path/$file");
        }

        rmdir($path);
    }
}
