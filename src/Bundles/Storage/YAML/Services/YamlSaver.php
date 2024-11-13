<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\YAML\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;
use Symfony\Component\Yaml\Yaml;

class YamlSaver
{
    use ArrayTrait;

    private int $yamlIndent;

    private bool $sortYaml;

    private bool $eolLast;



    public function __construct(int $yamlIndent, bool $sortYaml, bool $eolLast)
    {
        $this->yamlIndent = $yamlIndent;
        $this->sortYaml = $sortYaml;
        $this->eolLast = $eolLast;
    }



    public function saveTranslations(Locale $locale, string $delimiter, string $filename): int
    {
        $translationCount = 0;
        $saveValues = [];

        foreach ($locale->getTranslations() as $id => $translation) {
            $saveValues[$id] = $translation->getValue();
            $translationCount++;
        }

        if ($this->sortYaml) {
            ksort($saveValues);
        }

        $tmpArray = $this->getMultiDimensionalArray($saveValues, $delimiter);

        // Set inline to 10 to have the most cases covered. Maybe add an option later.
        $yaml = Yaml::dump($tmpArray, 10, $this->yamlIndent);

        # last EOL is optional, so let's remove it first
        $yaml = rtrim($yaml, PHP_EOL);

        if ($this->eolLast) {
            $yaml .= PHP_EOL;
        }

        file_put_contents($filename, $yaml);

        return $translationCount;
    }
}
