<?php

namespace phpunit\Bundles\Storage;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;

class StorageSaveResultTest extends TestCase
{

    /**
     * @return void
     */
    public function testSavedLocales(): void
    {
        $result = new StorageSaveResult(3, 4);

        $this->assertEquals(3, $result->getSavedLocales());
    }

    /**
     * @return void
     */
    public function testSavedTranslations(): void
    {
        $result = new StorageSaveResult(3, 4);

        $this->assertEquals(4, $result->getSavedTranslations());
    }
}
