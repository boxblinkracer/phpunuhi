<?php

namespace PHPUnuhi\Models\Command;

class CommandOption
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $hasValue;

    /**
     * @param string $name
     * @param bool $hasValue
     */
    public function __construct(string $name, bool $hasValue)
    {
        $this->name = $name;
        $this->hasValue = $hasValue;
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
    public function hasValue(): bool
    {
        return $this->hasValue;
    }

}
