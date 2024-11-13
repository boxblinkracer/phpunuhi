<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Loaders\File;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Loaders\File\FileLoader;

class FileLoaderTest extends TestCase
{
    private FileLoader $loader;


    private string $testFile;


    public function setUp(): void
    {
        $this->loader = new FileLoader();

        $this->testFile = __DIR__ . '/test_file.txt';
        file_put_contents($this->testFile, 'Sample content');
    }


    public function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }


    public function testLoadFileNotFound(): void
    {
        $nonExistentFile = 'path/to/non_existent_file.xml';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Configuration file not found: ' . $nonExistentFile);

        $this->loader->load($nonExistentFile);
    }

    /**
     * @throws Exception
     */
    public function testLoadFile(): void
    {
        $result = $this->loader->load($this->testFile);

        $this->assertEquals('Sample content', $result);
    }
}
