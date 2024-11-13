<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage;

class StorageHierarchy
{
    private bool $nestedStorage;

    private string $delimiter;


    public function __construct(bool $isNested, string $delimiter)
    {
        $this->nestedStorage = $isNested;
        $this->delimiter = $delimiter;
    }


    public function isNestedStorage(): bool
    {
        return $this->nestedStorage;
    }


    public function getDelimiter(): string
    {
        return $this->delimiter;
    }
}
