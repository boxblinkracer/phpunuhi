<?php

namespace PHPUnuhi\Services\Validation;

class JsonValidator implements ValidationInterface
{


    /**
     * @param array<string> $files
     * @return bool
     */
    public function validate(array $files): bool
    {
        $scopeSnippetCount = null;
        $foundSnippets = [];

        foreach ($files as $file) {


            $snippetJson = (string)file_get_contents($file);
            $snippetArray = json_decode($snippetJson, true);

            if ($snippetArray === false) {
                $snippetArray = [];
            }

            $snippetArrayFlat = $this->getFlatArray($snippetArray);

            $allKeys = array_keys($snippetArrayFlat);
            $allValues = array_values($snippetArrayFlat);

            if ($scopeSnippetCount === null) {
                # its our first
                $scopeSnippetCount = count($allKeys);
            }

            foreach ($allKeys as $key) {
                $value = $snippetArrayFlat[$key];
                if (empty($value)) {
                    return false;
                }
            }

            foreach ($allKeys as $key) {
                $foundSnippets[$file][] = $key;
            }
        }


        # NOW COMPARE THAT THEY HAVE THE SAME STRUCTURE
        # ACROSS ALL FILES

        $previousFile = '';
        $previousKeys = null;
        foreach ($foundSnippets as $file => $snippetKeys) {

            if ($previousKeys !== null) {

                if (!$this->arrayEqual($previousKeys, $snippetKeys)) {

                    echo "Found difference in snippets in these files: " . PHP_EOL;
                    echo "  - A: " . $previousFile . PHP_EOL;
                    echo "  - B: " . $file . PHP_EOL;

                    $filtered = array_diff($previousKeys, $snippetKeys);
                    foreach ($filtered as $key) {
                        echo '           [x]: ' . $key . PHP_EOL;
                    }

                    return false;
                }
            }

            $previousFile = $file;
            $previousKeys = $snippetKeys;
        }

        return true;
    }


    /**
     * @param array<mixed> $array
     * @param string $prefix
     * @return array<string>
     */
    private function getFlatArray(array $array, string $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->getFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private function arrayEqual($a, $b)
    {
        return (is_array($b)
            && is_array($a)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );


    }

}