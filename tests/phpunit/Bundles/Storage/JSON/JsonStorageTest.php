<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\JSON;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;

class JsonStorageTest extends TestCase
{
    private JsonStorage $storage;


    public function setUp(): void
    {
        $this->storage = new JsonStorage();
    }


    public function testStorageName(): void
    {
        $this->assertEquals('json', $this->storage->getStorageName());
    }


    public function testFileExtension(): void
    {
        $this->assertEquals('json', $this->storage->getFileExtension());
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
