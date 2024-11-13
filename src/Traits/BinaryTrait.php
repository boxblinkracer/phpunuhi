<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait BinaryTrait
{
    protected function binaryToString(string $binaryHex): string
    {
        return bin2hex($binaryHex);
    }


    protected function stringToBinary(string $text): string
    {
        $result = hex2bin($text);

        if ($result === '' || $result === '0' || $result === false) {
            return '';
        }

        return $result;
    }


    protected function isBinary(string $str): bool
    {
        return ! mb_check_encoding($str, 'UTF-8');
    }
}
