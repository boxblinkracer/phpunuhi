<?php

namespace phpunit\Models\Configuration\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;

class CaseStyleIgnoreKeyTest extends TestCase
{


    /**
     * @return void
     */
    public function testKey(): void
    {
        $key = new CaseStyleIgnoreKey('lblTitle', true);

        $this->assertEquals('lblTitle', $key->getKey());
    }

    /**
     * @return void
     */
    public function testIsFQP(): void
    {
        $key = new CaseStyleIgnoreKey('lblTitle', false);

        $this->assertEquals(false, $key->isFullyQualifiedPath());
    }
}
