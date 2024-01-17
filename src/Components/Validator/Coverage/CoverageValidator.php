<?php

namespace PHPUnuhi\Components\Validator\Coverage;

use PHPUnuhi\Models\Configuration\Coverage;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Coverage\CoverageService;

class CoverageValidator
{

    /**
     * @var CoverageService
     */
    private $coverageService;


    /**
     *
     */
    public function __construct()
    {
        $this->coverageService = new CoverageService();
    }


    /**
     * @param Coverage $coverageSetting
     * @param TranslationSet[] $translationSets
     * @return CoverageValidatorResult
     */
    public function validate(Coverage $coverageSetting, array $translationSets): CoverageValidatorResult
    {
        $result = $this->coverageService->getCoverage($translationSets);

        # -------------------------------------------------------------------------
        # GLOBAL TOTAL COVERAGE
        # make sure to use the total coverage from all TranslationSets
        # and compare it against our input value
        if ($coverageSetting->hasMinCoverage()) {
            $expectedMinCoverage = $coverageSetting->getMinCoverage();
            $actualTotalCoverage = $result->getCoverage();

            if ($actualTotalCoverage < $expectedMinCoverage) {
                return new CoverageValidatorResult(false, $expectedMinCoverage, $actualTotalCoverage, "global coverage for all sets");
            }
        }

        # -------------------------------------------------------------------------
        # GLOBAL LOCALE COVERAGES
        # Sum up all words from all locales across TranslationSets
        # and compare it against our input value
        foreach ($coverageSetting->getLocaleCoverages() as $configLocaleCoverage) {
            $expectedCov = $configLocaleCoverage->getMinCoverage();
            $tmpValue = $result->getLocaleCoverage($configLocaleCoverage->getLocale());

            if ($tmpValue < $expectedCov) {
                return new CoverageValidatorResult(
                    false,
                    $expectedCov,
                    $tmpValue,
                    "full coverage for locale '" . $configLocaleCoverage->getLocale() . "' across all TranslationSets"
                );
            }
        }

        # -------------------------------------------------------------------------
        # SET LOCALE TOTAL COVERAGE
        # we have a full coverage within a TranslationSet
        foreach ($translationSets as $translationSet) {
            $configTranslationSet = $coverageSetting->getTranslationSetCoverage($translationSet->getName());
            $actualTranslationSetCoverage = $result->getTranslationSetCoverage($translationSet->getName());

            $minCoverage = $configTranslationSet->getMinCoverage();

            if ($actualTranslationSetCoverage->getCoverage() < $minCoverage) {
                return new CoverageValidatorResult(
                    false,
                    $minCoverage,
                    $actualTranslationSetCoverage->getCoverage(),
                    "Total coverage of TranslationSet: " . $translationSet->getName()
                );
            }
        }

        # -------------------------------------------------------------------------
        # TRANSLATION SET LOCALE COVERAGES
        # Now a specific locale inside a TranslationSet needs a specific coverage value.
        foreach ($translationSets as $translationSet) {
            $configTranslationSet = $coverageSetting->getTranslationSetCoverage($translationSet->getName());
            $actualTranslationSetCoverage = $result->getTranslationSetCoverage($translationSet->getName());

            foreach ($translationSet->getLocales() as $locale) {
                if (!$configTranslationSet->hasLocaleCoverage($locale->getName())) {
                    continue;
                }

                $expectedCov = $configTranslationSet->getLocaleCoverage($locale->getName())->getMinCoverage();
                $tmpValue = $actualTranslationSetCoverage->getLocaleCoverage($locale->getName())->getCoverage();

                if ($tmpValue < $expectedCov) {
                    return new CoverageValidatorResult(
                        false,
                        $expectedCov,
                        $tmpValue,
                        "locale coverage of '" . $locale->getName() . "' for TranslationSet '" . $translationSet->getName() . "'"
                    );
                }
            }
        }

        return new CoverageValidatorResult(true, 0, 0, '');
    }
}
