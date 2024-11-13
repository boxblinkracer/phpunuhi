<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange;

class ImportEntry
{
    private string $localeExchangeID;

    private string $key;

    private string $group;

    private string $value;


    public function __construct(string $localeExchangeID, string $key, string $group, string $value)
    {
        $this->localeExchangeID = $localeExchangeID;
        $this->key = $key;
        $this->group = $group;
        $this->value = $value;
    }



    public function getLocaleExchangeID(): string
    {
        return $this->localeExchangeID;
    }


    public function getKey(): string
    {
        return $this->key;
    }


    public function getGroup(): string
    {
        return $this->group;
    }


    public function getValue(): string
    {
        return $this->value;
    }
}
