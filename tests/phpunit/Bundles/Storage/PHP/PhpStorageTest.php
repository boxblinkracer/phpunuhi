<?php

namespace phpunit\Bundles\Storage\PHP;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;

class PhpStorageTest extends TestCase
{

    /**
     * @var PhpStorage
     */
    private $storage;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storage = new PhpStorage();
    }

    /**
     * @return void
     */
    public function testStorageName(): void
    {
        $this->assertEquals('php', $this->storage->getStorageName());
    }

    /**
     * @return void
     */
    public function testFileExtension(): void
    {
        $this->assertEquals('php', $this->storage->getFileExtension());
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
