<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Command;

class CommandOption
{
    private string $name;

    private bool $hasValue;


    public function __construct(string $name, bool $hasValue)
    {
        $this->name = $name;
        $this->hasValue = $hasValue;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function hasValue(): bool
    {
        return $this->hasValue;
    }
}
