<?php

namespace PHPUnuhi\Bundles\Storage\YAML\Services;

use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;
use Symfony\Component\Yaml\Yaml;

class YamlSaver
{
    use ArrayTrait;

    /**
     * @var int
     */
    private $yamlIndent;

    /**
     * @var bool
     */
    private $sortYaml;

    /**
     * @param int $yamlIndent
     * @param bool $sortYaml
     */
    public function __construct(int $yamlIndent, bool $sortYaml)
    {
        $this->yamlIndent = $yamlIndent;
        $this->sortYaml = $sortYaml;
    }

    /**
     * @param TranslationSet $set
     * @param string $delimiter
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set, string $delimiter): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {
            $localeCount++;

            $saveValues = [];

            foreach ($locale->getTranslations() as $translation) {

                $saveValues[$translation->getID()] = $translation->getValue();
                $translationCount++;
            }

            if ($this->sortYaml) {
                ksort($saveValues);
            }

            $tmpArray = $this->getMultiDimensionalArray($saveValues, $delimiter);

            // Set inline to 10 to have the most cases covered. Maybe add an option later.
            $yaml = Yaml::dump($tmpArray, 10, $this->yamlIndent);

            file_put_contents($locale->getFilename(), $yaml);
        }

        return new StorageSaveResult($localeCount, $translationCount);
    }
}
