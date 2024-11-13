<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration\CaseStyle;

class CaseStyle
{
    private const MIN_LEVEL = 0;

    private string $name;

    private int $level = -1;


    public function __construct(string $name)
    {
        $this->name = $name;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function hasLevel(): bool
    {
        return $this->level >= self::MIN_LEVEL;
    }


    public function getLevel(): int
    {
        return $this->level;
    }


    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}
