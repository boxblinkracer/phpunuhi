<?php

declare(strict_types=1);

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
    private array $localeCoverages = [];



    public function setMinCoverage(float $totalMinCoverage): void
    {
        $this->minCoverage = $totalMinCoverage;

        $max = (Percentage::MAX_PERCENTAGE + self::OFFSET);

        if ($this->minCoverage >= $max) {
            $this->minCoverage = Percentage::MAX_PERCENTAGE;
        }
    }


    public function hasMinCoverage(): bool
    {
        return $this->minCoverage > self::COVERAGE_NOT_SET;
    }


    public function getMinCoverage(): float
    {
        return $this->minCoverage;
    }


    public function hasLocaleCoverage(string $locale): bool
    {
        return isset($this->localeCoverages[$locale]);
    }


    public function getLocaleCoverage(string $locale): LocaleCoverage
    {
        return $this->localeCoverages[$locale];
    }


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


    public function hasLocaleCoverages(): bool
    {
        return $this->localeCoverages !== [];
    }
}
