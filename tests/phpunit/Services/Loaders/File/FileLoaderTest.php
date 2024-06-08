<?php

namespace PHPUnuhi\Tests\Services\Loaders\File;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Loaders\File\FileLoader;

class FileLoaderTest extends TestCase
{

    /**
     * @var FileLoader
     */
    private $loader;

    /**
     * @var string
     */
    private $testFile;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->loader = new FileLoader();

        $this->testFile = __DIR__ . '/test_file.txt';
        file_put_contents($this->testFile, 'Sample content');
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    /**
     * @return void
     */
    public function testLoadFileNotFound(): void
    {
        $nonExistentFile = 'path/to/non_existent_file.xml';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Configuration file not found: ' . $nonExistentFile);

        $this->loader->load($nonExistentFile);
    }

    /**
     * @throws Exception
     * @return void
     */
    public function testLoadFile(): void
    {
        $result = $this->loader->load($this->testFile);

        $this->assertEquals('Sample content', $result);
    }
}
