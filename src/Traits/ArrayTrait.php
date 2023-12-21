<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait ArrayTrait
{

    /**
     * @param array<mixed> $array
     * @param string $delimiter
     * @param string $rootPrefix
     * @return array<mixed>
     */
    protected function getFlatArray(array $array, string $delimiter, string $rootPrefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $rootPrefix . ($rootPrefix === '' || $rootPrefix === '0' ? '' : $delimiter) . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->getFlatArray($value, $delimiter, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * # TODO it's working in most cases but not all the time if new lines exist on top of the file.
     *
     * @param array<mixed> $array
     * @param string $delimiter
     * @param string $rootPrefix
     * @param int $lineNumber
     * @param bool $closingBracket
     * @return int[]
     */
    protected function getLineNumbers(array $array, string $delimiter, string $rootPrefix = '', int $lineNumber = 0, bool $closingBracket = false): array
    {
        $result = [
            '__LINE_NUMBER__' => $lineNumber
        ];

        foreach ($array as $key => $value) {
            $result['__LINE_NUMBER__']++;
            $newKey = $rootPrefix . ($rootPrefix === '' || $rootPrefix === '0' ? '' : $delimiter) . $key;

            if (is_array($value)) {
                $result = array_merge(
                    $result,
                    $this->getLineNumbers(
                        $value,
                        $delimiter,
                        $newKey,
                        $result['__LINE_NUMBER__'],
                        $closingBracket
                    )
                );

                if ($closingBracket) {
                    $result['__LINE_NUMBER__']++;
                }
            } else {
                $result[$newKey] = $result['__LINE_NUMBER__'];
            }
        }

        return $result;
    }

    /**
     * @param array<mixed> $array
     * @param string $delimiter
     * @return array<mixed>
     */
    protected function getMultiDimensionalArray(array $array, string $delimiter): array
    {
        if ($delimiter === '') {
            return $array;
        }

        $result = [];
        foreach ($array as $notations => $value) {

            // extract keys
            $keys = explode($delimiter, $notations);

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
