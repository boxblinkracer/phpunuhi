<?php

namespace PHPUnuhi\Traits;

trait StringTrait
{

    /**
     * @param string $string
     * @param string $search
     * @return bool
     */
    protected function stringContains(string $string, string $search): bool
    {
        return (strpos($string, $search) !== false);
    }

    /**
     * @param string $string
     * @param string $startString
     * @return bool
     */
    protected function stringStartsWith(string $string, string $startString): bool
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    /**
     * @param string $string
     * @param string $endString
     * @return bool
     */
    protected function stringEndsWith(string $string, string $endString): bool
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }

}