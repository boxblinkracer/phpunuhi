<?php

namespace PHPUnuhi\Bundles\Storage\PHP\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;

class PHPLoader
{
    use ArrayTrait;

    /**
     * @param Locale $locale
     * @param string $delimiter
     * @return void
     */
    public function loadTranslation(Locale $locale, string $delimiter): void
    {
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
