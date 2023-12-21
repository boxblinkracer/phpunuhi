<?php

namespace PHPUnuhi\Models\Configuration;

class Filter
{

    /**
     * @var string[]
     */
    private $fieldsAllow = [];

    /**
     * @var string[]
     */
    private $fieldsExclude = [];

    /**
     * @return bool
     */
    public function hasFilters(): bool
    {
        if (count($this->fieldsAllow) > 0) {
            return true;
        }
        return count($this->fieldsExclude) > 0;
    }

    /**
     * @param string $key
     * @return void
     */
    public function addIncludeKey(string $key): void
    {
        if (in_array($key, $this->fieldsAllow)) {
            return;
        }

        $this->fieldsAllow[] = $key;
    }

    /**
     * @param string $key
     * @return void
     */
    public function addExcludeKey(string $key): void
    {
        if (in_array($key, $this->fieldsExclude)) {
            return;
        }

        $this->fieldsExclude[] = $key;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isKeyAllowed(string $key): bool
    {
        if (count($this->fieldsAllow) > 0) {
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

    /**
     * @param string $haystack
     * @param string $wildcard_pattern
     * @return bool
     */
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
