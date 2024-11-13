<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Writers\Directory;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Writers\Directory\DirectoryWriter;

class DirectoryWriterTest extends TestCase
{
    /**
     * @var string
     */
    protected $testDirectoryPath;



    protected function setUp(): void
    {
        $this->testDirectoryPath = __DIR__ . '/test_directory';
    }


    protected function tearDown(): void
    {
        if (is_dir($this->testDirectoryPath)) {
            $this->deleteDirectory($this->testDirectoryPath);
        }
    }



    public function testCreateDirectory(): void
    {
        $directoryWriter = new DirectoryWriter();

        $directoryWriter->createDirectory($this->testDirectoryPath);

        $this->assertDirectoryExists($this->testDirectoryPath);
    }


    public function testCreateDirectoryTwice(): void
    {
        $directoryWriter = new DirectoryWriter();

        $directoryWriter->createDirectory($this->testDirectoryPath);
        $directoryWriter->createDirectory($this->testDirectoryPath);

        $this->assertDirectoryExists($this->testDirectoryPath);
    }



    private function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);

        if ($files === false) {
            $files = [];
        }

        $files = array_diff($files, ['.', '..']);

        foreach ($files as $file) {
            is_dir("$path/$file") ? $this->deleteDirectory("$path/$file") : unlink("$path/$file");
        }

        rmdir($path);
    }
}
