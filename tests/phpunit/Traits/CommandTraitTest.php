<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\PHPUnuhi;
use PHPUnuhi\Traits\CommandTrait;

class CommandTraitTest extends TestCase
{
    use CommandTrait;


    /**
     * @return void
     */
    public function testShowHeader(): void
    {
        $this->expectOutputString("PHPUnuhi Framework, v" . PHPUnuhi::getVersion() . "\nCopyright (c) 2023 Christian Dangl\n\n");

        $this->showHeader();
    }
}
