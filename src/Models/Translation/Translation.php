<?php

namespace PHPUnuhi\Models\Translation;

class Translation
{

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $group;


    /**
     * @param string $key
     * @param string $value
     * @param string $group
     */
    public function __construct(string $key, string $value, string $group)
    {
        $this->key = $key;
        $this->value = $value;
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $newValue
     * @return void
     */
    public function setValue(string $newValue): void
    {
        $this->value = $newValue;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (trim($this->value) === '');
    }

}
