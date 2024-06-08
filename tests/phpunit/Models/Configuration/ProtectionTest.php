<?php

namespace PHPUnuhi\Tests\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Protection;

class ProtectionTest extends TestCase
{

    /**
     * @return void
     */
    public function testAddMarker(): void
    {
        $protection = new Protection();

        $protection->addMarker('<', '>');
        $protection->addMarker('[', ']');

        $this->assertEquals('<', $protection->getMarkers()[0]->getStart());
        $this->assertEquals('[', $protection->getMarkers()[1]->getStart());
    }

    /**
     * @return void
     */
    public function testAddTerm(): void
    {
        $protection = new Protection();

        $protection->addTerm('device');

        $this->assertEquals('device', $protection->getTerms()[0]);
    }

    /**
     * @return void
     */
    public function testAddTermDuplicateSkipped(): void
    {
        $protection = new Protection();

        $protection->addTerm('hardware');
        $protection->addTerm('device');
        $protection->addTerm('device');

        $this->assertCount(2, $protection->getTerms());
    }
}
