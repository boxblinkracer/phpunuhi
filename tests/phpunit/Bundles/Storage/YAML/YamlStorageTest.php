<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\YAML;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;

class YamlStorageTest extends TestCase
{
    private YamlStorage $storage;


    public function setUp(): void
    {
        $this->storage = new YamlStorage();
    }


    public function testStorageName(): void
    {
        $this->assertEquals('yaml', $this->storage->getStorageName());
    }


    public function testFileExtension(): void
    {
        $this->assertEquals('yaml', $this->storage->getFileExtension());
    }


    public function testSupportsFilter(): void
    {
        $this->assertFalse($this->storage->supportsFilters());
    }


    public function testHierarchy(): void
    {
        $hierarchy = $this->storage->getHierarchy();

        $this->assertTrue($hierarchy->isNestedStorage());
        $this->assertEquals('.', $hierarchy->getDelimiter());
    }
}
