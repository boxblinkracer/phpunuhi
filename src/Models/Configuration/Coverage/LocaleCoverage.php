<?php

namespace PHPUnuhi\Models\Configuration\Coverage;

class LocaleCoverage
{

    /**
     * @var string
     */
    private $locale;

    /**
     * @var int
     */
    private $minCoverage;


    /**
     * @param string $locale
     * @param int $minCoverage
     */
    public function __construct(string $locale, int $minCoverage)
    {
        $this->locale = $locale;
        $this->minCoverage = $minCoverage;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getMinCoverage(): int
    {
        return $this->minCoverage;
    }
}
