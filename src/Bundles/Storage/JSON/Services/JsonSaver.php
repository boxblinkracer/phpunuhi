<?php

namespace PHPUnuhi\Bundles\Storage\JSON\Services;

use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\ArrayTrait;

class
JsonSaver
{

    use ArrayTrait;


    /**
     * @var int
     */
    private $jsonIndent;

    /**
     * @var bool
     */
    private $sortJson;

    /**
     * @param int $jsonIndent
     * @param bool $sortJson
     */
    public function __construct(int $jsonIndent, bool $sortJson)
    {
        $this->jsonIndent = $jsonIndent;
        $this->sortJson = $sortJson;
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $indent = $this->jsonIndent;

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {

            $localeCount++;

            $saveValues = [];

            foreach ($locale->getTranslations() as $translation) {
                $saveValues[$translation->getID()] = $translation->getValue();
                $translationCount++;
            }

            if ($this->sortJson) {
                ksort($saveValues);
            }

            $tmpArray = $this->flattenToMultiDimensional($saveValues, '.');

            $jsonString = (string)json_encode($tmpArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $json = preg_replace_callback(
                '/^ +/m',
                function ($m) use ($indent) {
                    $indentStr = (string)str_repeat(' ', $indent);
                    $repeat = (int)(strlen($m[0]) / 2);
                    return str_repeat($indentStr, $repeat);
                },
                $jsonString
            );

            file_put_contents($locale->getFilename(), $json);
        }

        return new StorageSaveResult($localeCount, $translationCount);
    }

}