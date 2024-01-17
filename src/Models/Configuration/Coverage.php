<?php

namespace PHPUnuhi\Models\Configuration;

use PHPUnuhi\Models\Configuration\Coverage\LocaleCoverage;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;
use PHPUnuhi\Models\Percentage;

class Coverage
{
    private const COVERAGE_NOT_SET = -1;

    /**
     * @var float
     */
    private $minCoverage = self::COVERAGE_NOT_SET;

    /**
     * @var LocaleCoverage[]
     */
    private $localeCoverages = [];

    /**
     * @var TranslationSetCoverage[]
     */
    private $translationSetCoverages = [];


    /**
     * @param float $globalMinCoverage
     * @return void
     */
    public function setMinCoverage(float $globalMinCoverage): void
    {
        $this->minCoverage = $globalMinCoverage;

        if ($this->minCoverage >= Percentage::MAX_PERCENTAGE) {
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
     * @param string $locale
     * @return LocaleCoverage
     */
    public function getLocaleCoverage(string $locale): LocaleCoverage
    {
        return $this->localeCoverages[$locale];
    }

    /**
     * @return bool
     */
    public function hasLocaleCoverages(): bool
    {
        return count($this->localeCoverages) > 0;
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
     * @param string $name
     * @param TranslationSetCoverage $coverage
     * @return void
     */
    public function addTranslationSetCoverage(string $name, TranslationSetCoverage $coverage): void
    {
        $this->translationSetCoverages[$name] = $coverage;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTranslationSetCoverage(string $name): bool
    {
        return isset($this->translationSetCoverages[$name]);
    }

    /**
     * @param string $getName
     * @return TranslationSetCoverage
     */
    public function getTranslationSetCoverage(string $getName): TranslationSetCoverage
    {
        return $this->translationSetCoverages[$getName];
    }
}
