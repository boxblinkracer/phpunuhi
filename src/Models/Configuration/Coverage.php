<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

use PHPUnuhi\Models\Configuration\Coverage\LocaleCoverage;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;
use PHPUnuhi\Models\Percentage;

class Coverage
{
    private const COVERAGE_NOT_SET = -1;

    private const OFFSET = 0.1;

    /**
     * @var float
     */
    private $minCoverage = self::COVERAGE_NOT_SET;

    /**
     * @var LocaleCoverage[]
     */
    private array $localeCoverages = [];

    /**
     * @var TranslationSetCoverage[]
     */
    private array $translationSetCoverages = [];



    public function setMinCoverage(float $globalMinCoverage): void
    {
        $this->minCoverage = $globalMinCoverage;

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


    public function getLocaleCoverage(string $locale): LocaleCoverage
    {
        return $this->localeCoverages[$locale];
    }


    public function hasLocaleCoverages(): bool
    {
        return $this->localeCoverages !== [];
    }


    public function hasLocaleCoverage(string $locale): bool
    {
        return isset($this->localeCoverages[$locale]);
    }


    public function addTranslationSetCoverage(string $name, TranslationSetCoverage $coverage): void
    {
        $this->translationSetCoverages[$name] = $coverage;
    }


    public function hasTranslationSetCoverage(string $name): bool
    {
        return isset($this->translationSetCoverages[$name]);
    }


    public function getTranslationSetCoverage(string $getName): TranslationSetCoverage
    {
        return $this->translationSetCoverages[$getName];
    }
}
