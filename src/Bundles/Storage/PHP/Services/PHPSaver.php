<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\PHP\Services;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\ArrayTrait;

class PHPSaver
{
    use ArrayTrait;

    private bool $eolLast;


    public function __construct(bool $eolLast)
    {
        $this->eolLast = $eolLast;
    }



    public function saveLocale(Locale $locale, string $filename, string $delimiter, bool $sort): int
    {
        $translationCount = 0;

        $saveValues = [];

        $content = '';
        $content .= '<?php' . PHP_EOL;
        $content .= PHP_EOL;

        $content .= 'return [' . PHP_EOL;

        foreach ($locale->getTranslations() as $id => $translation) {
            $saveValues[$id] = $translation->getValue();
            $translationCount++;
        }

        if ($sort) {
            ksort($saveValues);
        }

        $tmpArray = $this->getMultiDimensionalArray($saveValues, $delimiter);

        $content .= $this->buildArray($tmpArray, 1);

        $content .= '];';

        if ($this->eolLast) {
            $content .= PHP_EOL;
        }

        file_put_contents($filename, $content);

        return $translationCount;
    }

    /**
     * @param array<mixed> $root
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
