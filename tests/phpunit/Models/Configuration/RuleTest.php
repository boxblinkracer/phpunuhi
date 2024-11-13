<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Rule;

class RuleTest extends TestCase
{
    public function testName(): void
    {
        $rule = new Rule('color-rule', 'black');

        $this->assertEquals('color-rule', $rule->getName());
    }


    public function testValue(): void
    {
        $rule = new Rule('color-rule', 'black');

        $this->assertEquals('black', $rule->getValue());
    }
}
