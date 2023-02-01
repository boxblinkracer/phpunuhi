<?php

namespace phpunit\Services\Maths;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Maths\PercentageCalculator;

class PercentageCalculatorTest extends TestCase
{

    /**
     *
     */
    public function testRoundedPercentage()
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(298, 300);

        $this->assertEquals(99.33, $result);
    }

    /**
     *
     */
    public function testRoundedPercentageDivByZero()
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(5, 0);

        $this->assertEquals(0, $result);
    }

    /**
     *
     */
    public function testEmptyMeansFull()
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(0, 0);

        $this->assertEquals(100, $result);
    }

}
