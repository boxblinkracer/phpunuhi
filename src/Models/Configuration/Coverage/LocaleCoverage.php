<?php

namespace PHPUnuhi\Models\Configuration\Coverage;

use PHPUnuhi\Models\Percentage;

class LocaleCoverage
{
    private const OFFSET = 0.1;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var float
     */
    private $minCoverage;


    /**
     * @param string $locale
     * @param float $minCoverage
     */
    public function __construct(string $locale, float $minCoverage)
    {
        $this->locale = $locale;
        $this->minCoverage = $minCoverage;

        $max = (Percentage::MAX_PERCENTAGE + self::OFFSET);

        if ($this->minCoverage >= $max) {
            $this->minCoverage = Percentage::MAX_PERCENTAGE;
        }
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return float
     */
    public function getMinCoverage(): float
    {
        return $this->minCoverage;
    }
}
