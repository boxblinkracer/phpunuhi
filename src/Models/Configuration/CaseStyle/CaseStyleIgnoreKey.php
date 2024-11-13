<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration\CaseStyle;

class CaseStyleIgnoreKey
{
    private string $key;

    private bool $fullyQualifiedPath;



    public function __construct(string $key, bool $fullyQualifiedPath)
    {
        $this->key = $key;
        $this->fullyQualifiedPath = $fullyQualifiedPath;
    }



    public function getKey(): string
    {
        return $this->key;
    }


    public function isFullyQualifiedPath(): bool
    {
        return $this->fullyQualifiedPath;
    }
}
