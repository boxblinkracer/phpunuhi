<?php

namespace phpunit;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\PHPUnuhi;

class PHPUnuhiTest extends TestCase
{

    /**
     * @return void
     */
    public function testVersion(): void
    {
        $this->assertNotEmpty(PHPUnuhi::getVersion());
        # a version number has a dot in it
        $this->assertStringContainsString('.', PHPUnuhi::getVersion());
    }
}
