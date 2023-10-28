<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Marker;

class MarkerTest extends TestCase
{

    /**
     * @return void
     */
    public function testStart(): void
    {
        $marker = new Marker('<', '>');

        $this->assertEquals('<', $marker->getStart());
    }

    /**
     * @return void
     */
    public function testEnd(): void
    {
        $marker = new Marker('<', '>');

        $this->assertEquals('>', $marker->getEnd());
    }

}
