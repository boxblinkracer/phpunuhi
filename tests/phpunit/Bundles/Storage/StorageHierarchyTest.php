<?php

namespace PHPUnuhi\Tests\Bundles\Storage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;

class StorageHierarchyTest extends TestCase
{

    /**
     * @return void
     */
    public function testNestedStorage(): void
    {
        $hierarchy = new StorageHierarchy(true, '.');

        $this->assertEquals(true, $hierarchy->isNestedStorage());
    }

    /**
     * @return void
     */
    public function testDelimiter(): void
    {
        $hierarchy = new StorageHierarchy(false, '+');

        $this->assertEquals('+', $hierarchy->getDelimiter());
    }
}
