<?php

namespace phpunit\Bundles\Storage\JSON;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;

class JsonStorageTest extends TestCase
{

    /**
     * @var JsonStorage
     */
    private $storage;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storage = new JsonStorage();
    }

    /**
     * @return void
     */
    public function testStorageName(): void
    {
        $this->assertEquals('json', $this->storage->getStorageName());
    }

    /**
     * @return void
     */
    public function testFileExtension(): void
    {
        $this->assertEquals('json', $this->storage->getFileExtension());
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
