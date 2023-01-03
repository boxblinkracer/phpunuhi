<?php

namespace SVRUnit\Commands;

use PHPUnuhi\PHPUnuhi;

trait CommandTrait
{

    /**
     * @return void
     */
    protected function showHeader()
    {
        echo "PHPUnuhi Framework, v" . PHPUnuhi::VERSION . PHP_EOL;
        echo "Copyright (c) 2023 Christian Dangl" . PHP_EOL;
        echo PHP_EOL;
    }

}