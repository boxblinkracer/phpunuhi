<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\PHPUnuhi;

class PHPUnuhiTest extends TestCase
{
    public function testVersion(): void
    {
        $this->assertNotEmpty(PHPUnuhi::getVersion());
        # a version number has a dot in it
        $this->assertStringContainsString('.', PHPUnuhi::getVersion());
    }
}
