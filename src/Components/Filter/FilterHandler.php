<?php

namespace PHPUnuhi\Components\Filter;

use PHPUnuhi\Models\Translation\TranslationSet;

class FilterHandler
{

    /**
     * @param TranslationSet $set
     * @return void
     */
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
