<?php

namespace phpunit\Models\Command;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Command\CommandOption;

class CommandOptionTest extends TestCase
{

    /**
     * @return void
     */
    public function testName(): void
    {
        $option = new CommandOption('delimiter', ',');

        $this->assertEquals('delimiter', $option->getName());
    }

    /**
     * @return void
     */
    public function testHasValue(): void
    {
        $option = new CommandOption('delimiter', ',');

        $this->assertEquals(true, $option->hasValue());
    }

    /**
     * @return void
     */
    public function testHasNoValue(): void
    {
        $option = new CommandOption('delimiter', '');

        $this->assertEquals(false, $option->hasValue());
    }
}
