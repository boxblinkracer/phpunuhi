<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreScope;

class CaseStyleIgnoreScopeTest extends TestCase
{
    public function testScopeGlobal(): void
    {
        $this->assertEquals('global', CaseStyleIgnoreScope::SCOPE_GLOBAL);
    }


    public function testScopeFixed(): void
    {
        $this->assertEquals('fixed', CaseStyleIgnoreScope::SCOPE_FIXED);
    }
}
