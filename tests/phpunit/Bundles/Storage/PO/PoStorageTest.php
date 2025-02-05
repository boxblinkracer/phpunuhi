<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\PO;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;

class PoStorageTest extends TestCase
{
    private PoStorage $storage;


    public function setUp(): void
    {
        $this->storage = new PoStorage();
    }


    public function testStorageName(): void
    {
        $this->assertEquals('po', $this->storage->getStorageName());
    }


    public function testFileExtension(): void
    {
        $this->assertEquals('po', $this->storage->getFileExtension());
    }


    public function testSupportsFilter(): void
    {
        $this->assertFalse($this->storage->supportsFilters());
    }


    public function testHierarchy(): void
    {
        $hierarchy = $this->storage->getHierarchy();

        $this->assertFalse($hierarchy->isNestedStorage());
        $this->assertEquals('', $hierarchy->getDelimiter());
    }

    public function testContentFileTemplate(): void
    {
        $content = $this->storage->getContentFileTemplate();
        $this->assertStringEqualsFile(__DIR__ . '/../../../../../src/Bundles/Storage/PO/Template/StorageFileTemplate.po', $content);
    }
}
