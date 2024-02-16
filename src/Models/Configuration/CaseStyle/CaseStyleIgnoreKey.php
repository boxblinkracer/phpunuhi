<?php

namespace PHPUnuhi\Models\Configuration\CaseStyle;

class CaseStyleIgnoreKey
{

    /**
     * @var string
     */
    private $key;

    /**
     * @var bool
     */
    private $fullyQualifiedPath;


    /**
     * @param string $key
     * @param bool $fullyQualifiedPath
     */
    public function __construct(string $key, bool $fullyQualifiedPath)
    {
        $this->key = $key;
        $this->fullyQualifiedPath = $fullyQualifiedPath;
    }


    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function isFullyQualifiedPath(): bool
    {
        return $this->fullyQualifiedPath;
    }
}
