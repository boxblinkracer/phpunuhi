<?php

namespace PHPUnuhi\Bundles\Translation\JSON;

use PHPUnuhi\Bundles\Translation\TranslateSaveResult;
use PHPUnuhi\Bundles\Translation\TranslationSaverInterface;
use PHPUnuhi\Models\Translation\TranslationSet;

class JSONTranslationSaver implements TranslationSaverInterface
{

    /**
     * @var int
     */
    private $jsonIntent;

    /**
     * @var bool
     */
    private $sortJson;

    /**
     * @param int $jsonIntent
     * @param bool $sortJson
     */
    public function __construct(int $jsonIntent, bool $sortJson)
    {
        $this->jsonIntent = $jsonIntent;
        $this->sortJson = $sortJson;
    }

    /**
     * @param TranslationSet $set
     * @return TranslateSaveResult
     */
    public function save(TranslationSet $set): TranslateSaveResult
    {
        $intent = $this->jsonIntent;

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {

            $localeCount++;

            $saveValues = [];

            foreach ($locale->getTranslations() as $translation) {
                $saveValues[$translation->getKey()] = $translation->getValue();
                $translationCount++;
            }

            if ($this->sortJson) {
                ksort($saveValues);
            }

            $tmpArray = $this->flattenToMultiDimensional($saveValues, '.');

            $jsonString = (string)json_encode($tmpArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $json = preg_replace_callback(
                '/^ +/m',
                function ($m) use ($intent) {
                    $intentStr = (string)str_repeat(' ', $intent);
                    $repeat = (int)(strlen($m[0]) / 2);
                    return str_repeat($intentStr, $repeat);
                },
                $jsonString
            );

            file_put_contents($locale->getFilename(), $json);
        }

        return new TranslateSaveResult($localeCount, $translationCount);
    }

    /**
     * @param array<mixed> $array
     * @param string $delimiter
     * @return array<mixed>
     */
    private function flattenToMultiDimensional(array $array, string $delimiter = '.'): array
    {
        $result = [];
        foreach ($array as $notations => $value) {
            // extract keys
            $keys = explode($delimiter, $notations);

            if ($keys === false) {
                $keys = [];
            }

            // reverse keys for assignments
            $keys = array_reverse($keys);


            // set initial value
            $lastVal = $value;
            foreach ($keys as $key) {
                // wrap value with key over each iteration
                $lastVal = [
                    $key => $lastVal
                ];
            }

            // merge result
            $result = array_merge_recursive($result, $lastVal);
        }

        return $result;
    }

}