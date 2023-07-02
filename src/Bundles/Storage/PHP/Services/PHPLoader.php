<?php

namespace PHPUnuhi\Bundles\Storage\PHP\Services;

use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class PHPLoader
{

    use ArrayTrait;

    /**
     * @param TranslationSet $set
     * @param string $delimiter
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set, string $delimiter): void
    {
        foreach ($set->getLocales() as $locale) {

            $arrayData = require($locale->getFilename());

            if (!is_array($arrayData)) {
                $arrayData = [];
            }

            $foundTranslationsFlat = $this->getFlatArray($arrayData, $delimiter);

            foreach ($foundTranslationsFlat as $key => $value) {
                $locale->addTranslation($key, $value, '');
            }

        }
    }

}