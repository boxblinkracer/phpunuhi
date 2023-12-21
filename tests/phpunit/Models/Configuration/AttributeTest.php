<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Attribute;

class AttributeTest extends TestCase
{

    /**
     * @return void
     */
    public function testName(): void
    {
        $attr = new Attribute('format', '');

        $this->assertEquals('format', $attr->getName());
    }

    /**
     * @return void
     */
    public function testValue(): void
    {
        $attr = new Attribute('format', 'ini');

        $this->assertEquals('ini', $attr->getValue());
    }
}
