<?php

namespace PHPUnuhi\Models\Configuration;

class CaseStyle
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $level;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->level = -1;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasLevel(): bool
    {
        return $this->level > -1;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

}