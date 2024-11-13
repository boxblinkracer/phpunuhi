<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\Shopware6;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;

class Shopware6StorageTest extends TestCase
{
    private Shopware6Storage $storage;


    public function setUp(): void
    {
        $this->storage = new Shopware6Storage();
    }


    public function testStorageName(): void
    {
        $this->assertEquals('shopware6', $this->storage->getStorageName());
    }


    public function testFileExtension(): void
    {
        $this->assertEquals('-', $this->storage->getFileExtension());
    }


    public function testSupportsFilter(): void
    {
        $this->assertTrue($this->storage->supportsFilters());
    }


    public function testHierarchy(): void
    {
        $hierarchy = $this->storage->getHierarchy();

        $this->assertFalse($hierarchy->isNestedStorage());
        $this->assertEquals('', $hierarchy->getDelimiter());
    }
}
