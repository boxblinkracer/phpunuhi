<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Filter;

use PHPUnuhi\Models\Translation\TranslationSet;

class FilterHandler
{
    public function applyFilter(TranslationSet $set): void
    {
        $filter = $set->getFilter();

        foreach ($set->getLocales() as $locale) {
            $translations = $locale->getTranslations();

            foreach ($translations as $translation) {
                if (!$filter->isKeyAllowed($translation->getKey())) {
                    $locale->removeTranslation($translation->getID());
                }
            }
        }
    }
}
