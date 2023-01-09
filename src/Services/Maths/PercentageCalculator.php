<?php

namespace PHPUnuhi\Services\Maths;

class PercentageCalculator
{

    /**
     * @param int $a
     * @param int $b
     * @return float
     */
    public function getRoundedPercentage(int $a, int $b): float
    {
        if ($b === 0) {
            return 0;
        }

        $percent = ($a / $b) * 100;

        return round($percent, 2);
    }

}
