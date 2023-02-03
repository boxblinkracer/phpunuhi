<?php

namespace PHPUnuhi\Bundles\Storage;

class StorageHierarchy
{

    /**
     * @var bool
     */
    private $multiLevel;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param bool $multiLevel
     * @param string $delimiter
     */
    public function __construct(bool $multiLevel, string $delimiter)
    {
        $this->multiLevel = $multiLevel;
        $this->delimiter = $delimiter;
    }

    /**
     * @return bool
     */
    public function isMultiLevel(): bool
    {
        return $this->multiLevel;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

}
