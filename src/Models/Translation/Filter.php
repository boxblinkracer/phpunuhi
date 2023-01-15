<?php

namespace PHPUnuhi\Models\Translation;

class Filter
{

    /**
     * @var string[]
     */
    private $fieldsAllow;

    /**
     * @var string[]
     */
    private $fieldsExclude;


    /**
     *
     */
    public function __construct()
    {
        $this->fieldsAllow = [];
        $this->fieldsExclude = [];
    }

    /**
     * @param string $key
     * @return void
     */
    public function addAllowKey(string $key): void
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
     * @param $wildcard_pattern
     * @param $haystack
     * @return false|int
     */
    private function stringMatchWithWildcard($haystack, $wildcard_pattern)
    {
        $regex = str_replace(
            array("\*", "\?"), // wildcard chars
            array('.*', '.'),   // regexp chars
            preg_quote($wildcard_pattern, '/')
        );

        return preg_match('/^' . $regex . '$/is', $haystack);
    }

}