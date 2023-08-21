<?php

namespace PHPUnuhi\Bundles\Storage\JSON\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;

class JsonSaver
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
     * @var bool
     */
    private $eolLast;

    /**
     * @param int $jsonIndent
     * @param bool $sortJson
     */
    public function __construct(int $jsonIndent, bool $sortJson, bool $eolLast)
    {
        $this->jsonIndent = $jsonIndent;
        $this->sortJson = $sortJson;
        $this->eolLast = $eolLast;
    }


    /**
     * @param Locale $locale
     * @param string $delimiter
     * @param string $filename
     * @return int
     */
    public function saveLocale(Locale $locale, string $delimiter, string $filename): int
    {
        $indent = $this->jsonIndent;

        $translationCount = 0;

        $saveValues = [];

        foreach ($locale->getTranslations() as $translation) {
            $saveValues[$translation->getID()] = $translation->getValue();
            $translationCount++;
        }

        if ($this->sortJson) {
            ksort($saveValues);
        }

        $tmpArray = $this->getMultiDimensionalArray($saveValues, $delimiter);

        $jsonString = (string)json_encode($tmpArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $indentStr = (string)str_repeat(' ', $indent);

        $json = preg_replace_callback(
            '/^ +/m',
            function ($m) use ($indentStr) {
                $repeat = (int)(strlen($m[0]) / 4);
                return str_repeat($indentStr, $repeat);
            },
            $jsonString
        );

        if ($this->eolLast) {
            $json .= PHP_EOL;
        }

        file_put_contents($filename, $json);

        return $translationCount;
    }

}
