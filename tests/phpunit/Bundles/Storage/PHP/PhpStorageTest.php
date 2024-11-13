<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage\PHP;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;

class PhpStorageTest extends TestCase
{
    private PhpStorage $storage;


    public function setUp(): void
    {
        $this->storage = new PhpStorage();
    }


    public function testStorageName(): void
    {
        $this->assertEquals('php', $this->storage->getStorageName());
    }


    public function testFileExtension(): void
    {
        $this->assertEquals('php', $this->storage->getFileExtension());
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
