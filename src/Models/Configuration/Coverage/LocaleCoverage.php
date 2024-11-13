<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration\Coverage;

use PHPUnuhi\Models\Percentage;

class LocaleCoverage
{
    private const OFFSET = 0.1;

    private string $locale;

    private float $minCoverage;



    public function __construct(string $locale, float $minCoverage)
    {
        $this->locale = $locale;
        $this->minCoverage = $minCoverage;

        $max = (Percentage::MAX_PERCENTAGE + self::OFFSET);

        if ($this->minCoverage >= $max) {
            $this->minCoverage = Percentage::MAX_PERCENTAGE;
        }
    }


    public function getLocale(): string
    {
        return $this->locale;
    }


    public function getMinCoverage(): float
    {
        return $this->minCoverage;
    }
}
