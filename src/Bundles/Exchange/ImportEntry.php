<?php

namespace PHPUnuhi\Bundles\Exchange;

class ImportEntry
{

    /**
     * @var string
     */
    private $localeExchangeID;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $localeExchangeID
     * @param string $key
     * @param string $group
     * @param string $value
     */
    public function __construct(string $localeExchangeID, string $key, string $group, string $value)
    {
        $this->localeExchangeID = $localeExchangeID;
        $this->key = $key;
        $this->group = $group;
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getLocaleExchangeID(): string
    {
        return $this->localeExchangeID;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
