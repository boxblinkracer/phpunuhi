<?php

declare(strict_types=1);

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
        return (string)hex2bin($text);
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isBinary(string $str): bool
    {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

}
