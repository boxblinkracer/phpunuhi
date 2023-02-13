<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\CaseStyle;

class CaseStyleTest extends TestCase
{

    /**
     * @return void
     */
    public function testName()
    {
        $style = new CaseStyle('pascal');

        $this->assertEquals('pascal', $style->getName());
    }

    /**
     * @return void
     */
    public function testDefaultLevel()
    {
        $style = new CaseStyle('pascal');

        $this->assertEquals(-1, $style->getLevel());
    }

    /**
     * @return void
     */
    public function testSetLevel()
    {
        $style = new CaseStyle('pascal');
        $style->setLevel(0);

        $this->assertEquals(0, $style->getLevel());
    }

    /**
     * @return void
     */
    public function testHasLevel()
    {
        $style = new CaseStyle('pascal');
        $style->setLevel(0);

        $this->assertEquals(true, $style->hasLevel());
    }

}
