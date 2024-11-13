<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;

class StorageHierarchyTest extends TestCase
{
    public function testNestedStorage(): void
    {
        $hierarchy = new StorageHierarchy(true, '.');

        $this->assertEquals(true, $hierarchy->isNestedStorage());
    }


    public function testDelimiter(): void
    {
        $hierarchy = new StorageHierarchy(false, '+');

        $this->assertEquals('+', $hierarchy->getDelimiter());
    }
}
