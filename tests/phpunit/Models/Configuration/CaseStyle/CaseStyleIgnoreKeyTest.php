<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;

class CaseStyleIgnoreKeyTest extends TestCase
{
    public function testKey(): void
    {
        $key = new CaseStyleIgnoreKey('lblTitle', true);

        $this->assertEquals('lblTitle', $key->getKey());
    }


    public function testIsFQP(): void
    {
        $key = new CaseStyleIgnoreKey('lblTitle', false);

        $this->assertEquals(false, $key->isFullyQualifiedPath());
    }
}
