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
        $result = hex2bin($text);

        if ($result === '' || $result === '0' || $result === false) {
            return '';
        }

        return $result;
    }

    /**
     * @param string $str
     * @return bool
     */
    protected function isBinary(string $str): bool
    {
        return ! mb_check_encoding($str, 'UTF-8');
    }
}
