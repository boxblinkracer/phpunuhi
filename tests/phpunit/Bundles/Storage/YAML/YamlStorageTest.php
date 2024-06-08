<?php

namespace PHPUnuhi\Tests\Bundles\Storage\YAML;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;

class YamlStorageTest extends TestCase
{

    /**
     * @var YamlStorage
     */
    private $storage;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storage = new YamlStorage();
    }

    /**
     * @return void
     */
    public function testStorageName(): void
    {
        $this->assertEquals('yaml', $this->storage->getStorageName());
    }

    /**
     * @return void
     */
    public function testFileExtension(): void
    {
        $this->assertEquals('yaml', $this->storage->getFileExtension());
    }

    /**
     * @return void
     */
    public function testSupportsFilter(): void
    {
        $this->assertFalse($this->storage->supportsFilters());
    }

    /**
     * @return void
     */
    public function testHierarchy(): void
    {
        $hierarchy = $this->storage->getHierarchy();

        $this->assertTrue($hierarchy->isNestedStorage());
        $this->assertEquals('.', $hierarchy->getDelimiter());
    }
}
