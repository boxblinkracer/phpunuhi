<?php

namespace PHPUnuhi\Models\Translation;

class Translation
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $group;


    /**
     * @param string $name
     * @param string $value
     * @param string $group
     */
    public function __construct(string $name, string $value, string $group)
    {
        $this->name = $name;
        $this->value = $value;
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        if (!empty($this->group)) {
            return $this->group . '.' . $this->name;
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
