<?php

namespace PHPUnuhi\Tests\Models\Command;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Command\CommandOption;

class CommandOptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testName(): void
    {
        $option = new CommandOption('delimiter', true);

        $this->assertEquals('delimiter', $option->getName());
    }

    /**
     * @return void
     */
    public function testHasValue(): void
    {
        $option = new CommandOption('delimiter', true);

        $this->assertEquals(true, $option->hasValue());
    }

    /**
     * @return void
     */
    public function testHasNoValue(): void
    {
        $option = new CommandOption('delimiter', false);

        $this->assertEquals(false, $option->hasValue());
    }
}
