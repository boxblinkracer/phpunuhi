<?php

namespace PHPUnuhi\Services\Coverage;

use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Coverage\Models\CoverageLocale;
use PHPUnuhi\Services\Coverage\Models\CoverageTotal;
use PHPUnuhi\Services\Coverage\Models\CoverageTranslationSet;

class CoverageService
{

    /**
     * @param TranslationSet[] $sets
     * @return CoverageTotal
     */
    public function getCoverage(array $sets): CoverageTotal
    {
        $tmpListSets = [];

        foreach ($sets as $set) {
            $tmpLocales = [];

            foreach ($set->getLocales() as $locale) {
                $tmpLocales[] = new CoverageLocale($locale);
            }

            $tmpListSets[] = new CoverageTranslationSet($set->getName(), $tmpLocales);
        }

        return new  CoverageTotal($tmpListSets);
    }
}
