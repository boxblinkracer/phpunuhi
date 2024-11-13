<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\PHP\Services;

use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class PHPLoader
{
    use ArrayTrait;


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

            // We start with three because the first three lines are the <?php tag and the return statement
            $locale->setLineNumbers(
                $this->getLineNumbers($arrayData, $delimiter, '', 3, true)
            );
        }
    }
}
