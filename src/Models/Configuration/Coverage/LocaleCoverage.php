<?php

namespace PHPUnuhi\Models\Configuration\Coverage;

use PHPUnuhi\Models\Percentage;

class LocaleCoverage
{

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

        if ($this->minCoverage >= Percentage::MAX_PERCENTAGE) {
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
