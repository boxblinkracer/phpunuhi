<?php

namespace PHPUnuhi\Bundles\Storage\PHP;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class PhpStorage implements StorageInterface
{

    use ArrayTrait;

    /**
     * @var bool
     */
    private $sort;


    /**
     * @param bool $sort
     */
    public function __construct(bool $sort)
    {
        $this->sort = $sort;
    }


    /**
     * @return bool
     */
    public function supportsFilters(): bool
    {
        return false;
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function loadTranslations(TranslationSet $set): void
    {
        foreach ($set->getLocales() as $locale) {
            $arrayData = require($locale->getFilename());

            if (!is_array($arrayData)) {
                $arrayData = [];
            }

            $foundTranslationsFlat = $this->getFlatArray($arrayData);

            foreach ($foundTranslationsFlat as $key => $value) {
                $locale->addTranslation($key, $value, '');
            }
        }
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;


        foreach ($set->getLocales() as $locale) {

            $localeCount++;

            $saveValues = [];


            $content = '';

            $content .= '<?php' . PHP_EOL;
            $content .= PHP_EOL;

            $content .= 'return [' . PHP_EOL;


            foreach ($locale->getTranslations() as $translation) {
                $saveValues[$translation->getID()] = $translation->getValue();
                $translationCount++;
            }

            if ($this->sort) {
                ksort($saveValues);
            }

            $tmpArray = $this->getMultiDimensionalArray($saveValues, '.');

            $content .= $this->buildArray($tmpArray, 1);


            $content .= '];' . PHP_EOL;

            file_put_contents($locale->getFilename(), $content);
        }


        return new StorageSaveResult($localeCount, $translationCount);
    }

    /**
     * @param array<mixed> $root
     * @param int $indent
     * @return string
     */
    private function buildArray(array $root, int $indent): string
    {
        $content = "";

        $indentStr = str_repeat('    ', $indent);


        foreach ($root as $key => $value) {

            if (is_array($value)) {
                $indent += 1;

                $content .= $indentStr . '"' . $key . '" => [' . PHP_EOL;
                $content .= $this->buildArray($value, $indent);
                $content .= $indentStr . '],' . PHP_EOL;
            } else {
                $content .= $indentStr . '"' . $key . '" => "' . $value . '",' . PHP_EOL;
            }
        }

        return $content;
    }
}
