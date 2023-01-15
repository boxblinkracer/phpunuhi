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
        # if we have an allow list,
        # then only verify this
        if (count($this->fieldsAllow) > 0) {
            return in_array($key, $this->fieldsAllow);
        }

        # otherwise check if its at least not excluded
        return !in_array($key, $this->fieldsExclude);
    }

}