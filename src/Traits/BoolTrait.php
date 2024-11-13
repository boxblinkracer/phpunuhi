<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait BoolTrait
{
    protected function getBool(string $string): bool
    {
        return strtolower($string) === 'true';
    }
}
