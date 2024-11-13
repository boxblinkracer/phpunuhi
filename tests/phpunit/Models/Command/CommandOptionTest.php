<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Command;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Command\CommandOption;

class CommandOptionTest extends TestCase
{
    public function testName(): void
    {
        $option = new CommandOption('delimiter', true);

        $this->assertEquals('delimiter', $option->getName());
    }


    public function testHasValue(): void
    {
        $option = new CommandOption('delimiter', true);

        $this->assertEquals(true, $option->hasValue());
    }


    public function testHasNoValue(): void
    {
        $option = new CommandOption('delimiter', false);

        $this->assertEquals(false, $option->hasValue());
    }
}
