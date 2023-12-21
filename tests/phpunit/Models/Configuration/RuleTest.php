<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Rule;

class RuleTest extends TestCase
{

    /**
     * @return void
     */
    public function testName(): void
    {
        $rule = new Rule('color-rule', 'black');

        $this->assertEquals('color-rule', $rule->getName());
    }

    /**
     * @return void
     */
    public function testValue(): void
    {
        $rule = new Rule('color-rule', 'black');

        $this->assertEquals('black', $rule->getValue());
    }
}
