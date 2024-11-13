<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Attribute;

class AttributeTest extends TestCase
{
    public function testName(): void
    {
        $attr = new Attribute('format', '');

        $this->assertEquals('format', $attr->getName());
    }


    public function testValue(): void
    {
        $attr = new Attribute('format', 'ini');

        $this->assertEquals('ini', $attr->getValue());
    }
}
