<?php

namespace PHPUnuhi\Tests\Services\Maths;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Maths\PercentageCalculator;

class PercentageCalculatorTest extends TestCase
{

    /**
     *
     */
    public function testRoundedPercentage(): void
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(298, 300);

        $this->assertEquals(99.33, $result);
    }

    /**
     *
     */
    public function testRoundedPercentageDivByZero(): void
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(5, 0);

        $this->assertEquals(0, $result);
    }

    /**
     *
     */
    public function testEmptyMeansFull(): void
    {
        $calculator = new PercentageCalculator();

        $result = $calculator->getRoundedPercentage(0, 0);

        $this->assertEquals(100, $result);
    }
}
