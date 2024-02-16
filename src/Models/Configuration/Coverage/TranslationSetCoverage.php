<?php

namespace PHPUnuhi\Models\Configuration\Coverage;

use PHPUnuhi\Models\Percentage;

class TranslationSetCoverage
{
    private const COVERAGE_NOT_SET = -1;

    private const OFFSET = 0.1;

    /**
     * @var float
     */
    private $minCoverage = self::COVERAGE_NOT_SET;

    /**
     * @var array<LocaleCoverage>
     */
    private $localeCoverages = [];


    /**
     * @param float $totalMinCoverage
     * @return void
     */
    public function setMinCoverage(float $totalMinCoverage): void
    {
        $this->minCoverage = $totalMinCoverage;

        $max = (Percentage::MAX_PERCENTAGE + self::OFFSET);

        if ($this->minCoverage >= $max) {
            $this->minCoverage = Percentage::MAX_PERCENTAGE;
        }
    }

    /**
     * @return bool
     */
    public function hasMinCoverage(): bool
    {
        return $this->minCoverage > self::COVERAGE_NOT_SET;
    }

    /**
     * @return float
     */
    public function getMinCoverage(): float
    {
        return $this->minCoverage;
    }

    /**
     * @param string $locale
     * @return bool
     */
    public function hasLocaleCoverage(string $locale): bool
    {
        return isset($this->localeCoverages[$locale]);
    }

    /**
     * @param string $locale
     * @return LocaleCoverage
     */
    public function getLocaleCoverage(string $locale): LocaleCoverage
    {
        return $this->localeCoverages[$locale];
    }

    /**
     * @param string $locale
     * @param float $minCoverage
     * @return void
     */
    public function addLocaleCoverage(string $locale, float $minCoverage): void
    {
        $this->localeCoverages[$locale] = new LocaleCoverage($locale, $minCoverage);
    }

    /**
     * @return LocaleCoverage[]
     */
    public function getLocaleCoverages(): array
    {
        return $this->localeCoverages;
    }

    /**
     * @return bool
     */
    public function hasLocaleCoverages(): bool
    {
        return count($this->localeCoverages) > 0;
    }
}
