<?php

namespace PHPUnuhi\Models\Configuration\Coverage;

class Coverage
{
    private const COVERAGE_NOT_SET = -1;

    /**
     * @var int
     */
    private $totalMinCoverage = self::COVERAGE_NOT_SET;

    /**
     * @var array<LocaleCoverage>
     */
    private $localeCoverages = [];


    /**
     * @param int $totalMinCoverage
     * @return void
     */
    public function setTotalMinCoverage(int $totalMinCoverage): void
    {
        $this->totalMinCoverage = $totalMinCoverage;
    }

    /**
     * @return bool
     */
    public function hasTotalMinCoverage(): bool
    {
        return $this->totalMinCoverage > self::COVERAGE_NOT_SET;
    }

    /**
     * @return int
     */
    public function getTotalMinCoverage(): int
    {
        return $this->totalMinCoverage;
    }

    /**
     * @param string $locale
     * @return bool
     */
    public function hasLocaleCoverage(string $locale): bool
    {
        return isset($this->localeCoverages[$locale]);
    }

    public function getLocaleCoverage(string $locale): LocaleCoverage
    {
        return $this->localeCoverages[$locale];
    }

    /**
     * @param string $locale
     * @param int $minCoverage
     * @return void
     */
    public function addLocaleCoverage(string $locale, int $minCoverage): void
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
