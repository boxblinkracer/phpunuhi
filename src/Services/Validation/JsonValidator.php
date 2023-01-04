<?php

namespace PHPUnuhi\Services\Validation;

use PHPUnuhi\Models\Translation\TranslationSuite;

class JsonValidator implements ValidationInterface
{

    /**
     * @param TranslationSuite $suite
     * @return bool
     */
    public function validate(TranslationSuite $suite): bool
    {
        $foundSnippets = [];

        $isValid = true;

        foreach ($suite->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $foundSnippets[$locale->getFilename()][] = $translation->getKey();
            }
        }


        # NOW COMPARE THAT THEY HAVE THE SAME STRUCTURE ACROSS ALL FILES

        $previousFile = '';
        $previousKeys = null;

        $allKeys = $suite->getAllTranslationKeys();

        foreach ($suite->getLocales() as $locale) {

            $localeKeys = $locale->getTranslationKeys();

            # verify if our current locale has the same structure
            # as our global suite keys list
            $structureValid = $this->isStructureEqual($localeKeys, $allKeys);

            if (!$structureValid) {

                echo "Found different structure in this file: " . PHP_EOL;
                echo "  - " . $locale->getFilename() . PHP_EOL;

                $filtered = $this->getDiff($localeKeys, $allKeys);

                foreach ($filtered as $key) {
                    echo '           [x]: ' . $key . PHP_EOL;
                }
                echo PHP_EOL;

                $isValid = false;
            }
        }


        foreach ($suite->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                if (empty($translation->getValue())) {
                    echo "Found empty translation in this file: " . PHP_EOL;
                    echo "  - " . $locale->getFilename() . PHP_EOL;
                    echo '           [x]: ' . $translation->getKey() . PHP_EOL;
                    echo PHP_EOL;
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private function isStructureEqual($a, $b)
    {
        return (is_array($b)
            && is_array($a)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     * @return array<mixed>
     */
    private function getDiff(array $a, array $b): array
    {
        $diffA = array_diff($a, $b);
        $diffB = array_diff($b, $a);

        return array_merge($diffA, $diffB);
    }
}
