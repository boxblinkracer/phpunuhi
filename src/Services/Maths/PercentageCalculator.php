<?php

namespace PHPUnuhi\Services\Maths;

class PercentageCalculator
{

    /**
     *
     */
    private const MAX_PERCENTAGE = 100;

    /**
     * @param int $numberExisting
     * @param int $numberTotal
     * @return float
     */
    public function getRoundedPercentage(int $numberExisting, int $numberTotal): float
    {
        if ($numberExisting === 0 && $numberTotal === 0) {
            return self::MAX_PERCENTAGE;
        }

        if ($numberTotal === 0) {
            return 0;
        }

        $percent = ($numberExisting / $numberTotal) * 100;

        return round($percent, 2);
    }
}
