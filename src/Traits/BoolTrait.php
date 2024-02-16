<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait BoolTrait
{

    /**
     * @param string $string
     * @return bool
     */
    protected function getBool(string $string): bool
    {
        return strtolower($string) === 'true';
    }
}
