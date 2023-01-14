<?php

namespace PHPUnuhi\Traits;

trait BinaryTrait
{

    /**
     * @param string $binaryHex
     * @return string
     */
    protected function binaryToString(string $binaryHex): string
    {
        $parts = unpack("H*", $binaryHex);

        if ($parts === false) {
            return '';
        }

        return implode($parts);
    }

    /**
     * @param mixed $str
     * @return bool
     */
    protected function isBinary($str): bool
    {
        if ($str === null) {
            return false;
        }

        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

}