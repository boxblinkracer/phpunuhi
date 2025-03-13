<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

trait StringTrait
{
    protected function stringDoesContain(string $string, string $search): bool
    {
        return (strpos($string, $search) !== false);
    }


    protected function stringDoesStartsWith(string $string, string $startString): bool
    {
        return 0 === strpos($string, $startString);
    }


    protected function stringDoesEndsWith(string $string, string $endString): bool
    {
        $len = strlen($endString);
        if ($len === 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }

    /**
     * @param string[] $search
     */
    protected function stringDoesContainInArray(string $string, array $search): bool
    {
        foreach ($search as $item) {
            if ($this->stringDoesContain($string, $item)) {
                return true;
            }
        }
        return false;
    }
}
