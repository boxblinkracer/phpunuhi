<?php

namespace PHPUnuhi\Bundles\Storage\JSON\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;


class JsonLoader
{

    use ArrayTrait;


    /**
     * @param Locale $locale
     * @param string $delimiter
     * @return void
     */
    public function loadTranslations(Locale $locale, string $delimiter): void
    {
        $snippetJson = (string)file_get_contents($locale->getFilename());

        $foundTranslations = [];

        if (!empty($snippetJson)) {
            $foundTranslations = json_decode($snippetJson, true);

            if ($foundTranslations === false) {
                $foundTranslations = [];
            }
        }

        if ($foundTranslations === null) {
            $foundTranslations = [];
        }

        $foundTranslationsFlat = $this->getFlatArray($foundTranslations, $delimiter);

        foreach ($foundTranslationsFlat as $key => $value) {
            $locale->addTranslation($key, $value, '');
        }
    }

}