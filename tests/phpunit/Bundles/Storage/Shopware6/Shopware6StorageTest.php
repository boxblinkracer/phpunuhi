<?php

namespace PHPUnuhi\Tests\Bundles\Storage\Shopware6;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;

class Shopware6StorageTest extends TestCase
{

    /**
     * @var Shopware6Storage
     */
    private $storage;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storage = new Shopware6Storage();
    }

    /**
     * @return void
     */
    public function testStorageName(): void
    {
        $this->assertEquals('shopware6', $this->storage->getStorageName());
    }

    /**
     * @return void
     */
    public function testFileExtension(): void
    {
        $this->assertEquals('-', $this->storage->getFileExtension());
    }

    /**
     * @return void
     */
    public function testSupportsFilter(): void
    {
        $this->assertTrue($this->storage->supportsFilters());
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
