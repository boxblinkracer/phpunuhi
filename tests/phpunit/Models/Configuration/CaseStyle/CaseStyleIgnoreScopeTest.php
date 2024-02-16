<?php

namespace phpunit\Models\Configuration\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreScope;

class CaseStyleIgnoreScopeTest extends TestCase
{


    /**
     * @return void
     */
    public function testScopeGlobal(): void
    {
        $this->assertEquals('global', CaseStyleIgnoreScope::SCOPE_GLOBAL);
    }

    /**
     * @return void
     */
    public function testScopeFixed(): void
    {
        $this->assertEquals('fixed', CaseStyleIgnoreScope::SCOPE_FIXED);
    }
}
