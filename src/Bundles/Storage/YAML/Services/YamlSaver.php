<?php

namespace PHPUnuhi\Bundles\Storage\YAML\Services;

use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Path\FileExtensionConverter;
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
     * @var bool
     */
    private $eolLast;


    /**
     * @param int $yamlIndent
     * @param bool $sortYaml
     * @param bool $eolLast
     */
    public function __construct(int $yamlIndent, bool $sortYaml, bool $eolLast)
    {
        $this->yamlIndent = $yamlIndent;
        $this->sortYaml = $sortYaml;
        $this->eolLast = $eolLast;
    }


    /**
     * @param Locale $locale
     * @param string $delimiter
     * @param string $filename
     * @return int
     */
    public function saveTranslations(Locale $locale, string $delimiter, string $filename): int
    {
        $translationCount = 0;
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

        # last EOL is optional, so let's remove it first
        $yaml = rtrim($yaml, PHP_EOL);

        if ($this->eolLast) {
            $yaml .= PHP_EOL;
        }

        file_put_contents($filename, $yaml);

        return $translationCount;
    }
}
