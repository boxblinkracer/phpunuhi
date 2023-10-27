<?php

namespace PHPUnuhi\Bundles\Storage\YAML\Services;

use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;
use Symfony\Component\Yaml\Yaml;

class YamlLoader
{
    use ArrayTrait;

    /**
     * @var Filter $filter
     */
    private $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param Locale $locale
     * @param string $delimiter
     *
     * @return void
     */
    public function loadLocale(Locale $locale, string $delimiter): void
    {
        $arrayData = Yaml::parseFile($locale->getFilename());

        if (!is_array($arrayData)) {
            $arrayData = [];
        }

        $translations = $this->getFlatArray($arrayData, $delimiter, '');
        $filteredTranslations = [];

        if ($this->filter instanceof Filter) {
            [$translations, $filteredTranslations] = $this->getFilteredResult($translations, $this->filter);
        }

        foreach ($translations as $key => $value) {
            # empty yaml values are NULL, so we cast it
            $locale->addTranslation($key, (string)$value, '');
        }

        $locale->setFilteredKeys($filteredTranslations);
        $locale->setLineNumbers(
            $this->getLineNumbers($arrayData, $delimiter)
        );
    }
}
