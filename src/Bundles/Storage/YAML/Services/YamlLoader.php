<?php

namespace PHPUnuhi\Bundles\Storage\YAML\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;
use Symfony\Component\Yaml\Yaml;

class YamlLoader
{
    use ArrayTrait;

    /**
     * @param Locale $locale
     * @param string $delimiter
     *
     * @return void
     */
    public function loadTranslations(Locale $locale, string $delimiter): void
    {
        $arrayData = Yaml::parseFile($locale->getFilename());

        if (!is_array($arrayData)) {
            $arrayData = [];
        }

        $foundTranslationsFlat = $this->getFlatArray($arrayData, $delimiter);

        foreach ($foundTranslationsFlat as $key => $value) {
            $locale->addTranslation($key, $value, '');
        }
    }
}
