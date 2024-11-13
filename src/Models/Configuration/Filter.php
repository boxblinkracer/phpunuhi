<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

class Filter
{
    /**
     * @var string[]
     */
    private array $fieldsAllow = [];

    /**
     * @var string[]
     */
    private array $fieldsExclude = [];


    public function hasFilters(): bool
    {
        if ($this->fieldsAllow !== []) {
            return true;
        }
        return $this->fieldsExclude !== [];
    }


    public function addIncludeKey(string $key): void
    {
        if (in_array($key, $this->fieldsAllow)) {
            return;
        }

        $this->fieldsAllow[] = $key;
    }


    public function addExcludeKey(string $key): void
    {
        if (in_array($key, $this->fieldsExclude)) {
            return;
        }

        $this->fieldsExclude[] = $key;
    }


    public function isKeyAllowed(string $key): bool
    {
        if ($this->fieldsAllow !== []) {
            foreach ($this->fieldsAllow as $fieldPattern) {
                if ($this->stringMatchWithWildcard($key, $fieldPattern)) {
                    return true;
                }
            }
            return false;
        }

        # otherwise check if its at least not excluded
        foreach ($this->fieldsExclude as $fieldPattern) {
            if ($this->stringMatchWithWildcard($key, $fieldPattern)) {
                return false;
            }
        }

        return true;
    }


    private function stringMatchWithWildcard(string $haystack, string $wildcard_pattern): bool
    {
        $regex = str_replace(
            ["\*", "\?"], // wildcard chars
            ['.*', '.'],   // regexp chars
            preg_quote($wildcard_pattern, '/')
        );

        $result = preg_match('/^' . $regex . '$/is', $haystack);
        return $result === 1;
    }
}
