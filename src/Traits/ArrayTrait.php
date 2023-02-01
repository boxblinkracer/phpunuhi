<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait ArrayTrait
{

    /**
     * @param array<mixed> $array
     * @param string $prefix
     * @return array<mixed>
     */
    protected function getFlatArray(array $array, string $prefix = ''): array
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
     * @param array<mixed> $array
     * @param string $delimiter
     * @return array<mixed>
     */
    protected function getMultiDimensionalArray(array $array, string $delimiter = '.'): array
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
