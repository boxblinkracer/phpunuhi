<?php

namespace phpunit\Bundles\Storage\PO;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;

class PoStorageTest extends TestCase
{

    /**
     * @var PoStorage
     */
    private $storage;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storage = new PoStorage();
    }

    /**
     * @return void
     */
    public function testStorageName(): void
    {
        $this->assertEquals('po', $this->storage->getStorageName());
    }

    /**
     * @return void
     */
    public function testFileExtension(): void
    {
        $this->assertEquals('po', $this->storage->getFileExtension());
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

        $this->assertFalse($hierarchy->isNestedStorage());
        $this->assertEquals('', $hierarchy->getDelimiter());
    }
}
