<?php

namespace PHPUnuhi\Bundles\Storage;

class StorageHierarchy
{

    /**
     * @var bool
     */
    private $nestedStorage;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param bool $isNested
     * @param string $delimiter
     */
    public function __construct(bool $isNested, string $delimiter)
    {
        $this->nestedStorage = $isNested;
        $this->delimiter = $delimiter;
    }

    /**
     * @return bool
     */
    public function isNestedStorage(): bool
    {
        return $this->nestedStorage;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }
}
