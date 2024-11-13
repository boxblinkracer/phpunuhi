<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Marker;

class MarkerTest extends TestCase
{
    public function testStart(): void
    {
        $marker = new Marker('<', '>');

        $this->assertEquals('<', $marker->getStart());
    }


    public function testEnd(): void
    {
        $marker = new Marker('<', '>');

        $this->assertEquals('>', $marker->getEnd());
    }
}
