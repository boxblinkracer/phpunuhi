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
        return bin2hex($binaryHex);
    }

    /**
     * @param string $text
     * @return string
     */
    protected function stringToBinary(string $text): string
    {
        $result = hex2bin($text);

        if ($result === false) {
            return '';
        }

        return $result;
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