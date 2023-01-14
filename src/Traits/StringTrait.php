<?php

namespace PHPUnuhi\Traits;

trait StringTrait
{

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