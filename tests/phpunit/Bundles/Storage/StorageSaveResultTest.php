<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Storage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;

class StorageSaveResultTest extends TestCase
{
    public function testSavedLocales(): void
    {
        $result = new StorageSaveResult(3, 4);

        $this->assertEquals(3, $result->getSavedLocales());
    }


    public function testSavedTranslations(): void
    {
        $result = new StorageSaveResult(3, 4);

        $this->assertEquals(4, $result->getSavedTranslations());
    }
}
