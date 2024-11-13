<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;

class CaseStyleTest extends TestCase
{
    public function testName(): void
    {
        $style = new CaseStyle('pascal');

        $this->assertEquals('pascal', $style->getName());
    }


    public function testDefaultLevel(): void
    {
        $style = new CaseStyle('pascal');

        $this->assertEquals(-1, $style->getLevel());
    }


    public function testSetLevel(): void
    {
        $style = new CaseStyle('pascal');
        $style->setLevel(0);

        $this->assertEquals(0, $style->getLevel());
    }


    public function testHasLevel(): void
    {
        $style = new CaseStyle('pascal');
        $style->setLevel(0);

        $this->assertEquals(true, $style->hasLevel());
    }
}
