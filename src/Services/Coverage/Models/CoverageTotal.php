<?php

namespace PHPUnuhi\Services\Coverage\Models;

use PHPUnuhi\Services\Coverage\Traits\CoverageDataTrait;
use PHPUnuhi\Services\Maths\PercentageCalculator;
use RuntimeException;

class CoverageTotal
{
    use CoverageDataTrait;

    /**
     * @var CoverageTranslationSet[]
     */
    private $translationSets;


    /**
     * @param CoverageTranslationSet[] $coverageSets
     */
    public function __construct(array $coverageSets)
    {
        $this->translationSets = $coverageSets;

        $this->calculate();
    }

    /**
     * @return CoverageTranslationSet[]
     */
    public function getTranslationSetCoverages(): array
    {
        return $this->translationSets;
    }

    /**
     * @param string $translationSetName
     * @return CoverageTranslationSet
     */
    public function getTranslationSetCoverage(string $translationSetName): CoverageTranslationSet
    {
        foreach ($this->translationSets as $coverageSet) {
            if ($coverageSet->getName() === $translationSetName) {
                return $coverageSet;
            }
        }

        throw new RuntimeException('CoverageSet not found');
    }

    /**
     * @param string $locale
     * @return float
     */
    public function getLocaleCoverage(string $locale): float
    {
        $fullWords = 0;
        $fullTranslated = 0;

        foreach ($this->translationSets as $tmpCoverage) {
            foreach ($tmpCoverage->getLocaleCoverages() as $tmpLocale) {
                if ($tmpLocale->getLocaleName() === $locale) {
                    $fullWords += $tmpLocale->getWordCount();
                    $fullTranslated += $tmpLocale->getCountTranslated();
                }
            }
        }

        if ($fullWords === 0) {
            return 0;
        }

        $calculator = new PercentageCalculator();

        return $calculator->getRoundedPercentage($fullTranslated, $fullWords);
    }

    /**
     * @return void
     */
    private function calculate(): void
    {
        $this->countTranslated = 0;
        $this->countAll = 0;
        $this->countWords = 0;

        foreach ($this->translationSets as $coverage) {
            $this->countTranslated += $coverage->getCountTranslated();
            $this->countAll += $coverage->getCountAll();
            $this->countWords += $coverage->getWordCount();
        }
    }
}
