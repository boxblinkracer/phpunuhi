<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Translation;

use PHPUnuhi\Traits\StringTrait;

class Translation
{
    use StringTrait;

    private string $key;

    private string $value;

    private string $group;



    public function __construct(string $key, string $value, string $group)
    {
        $this->key = $key;
        $this->value = $value;
        $this->group = $group;
    }

    /**
     * Gets the ID of the translation.
     * This one is unique within a locale.
     */
    public function getID(): string
    {
        if ($this->group !== '' && $this->group !== '0') {
            return 'group--' . $this->group . '.' . $this->key;
        }

        return $this->key;
    }

    /**
     * Gets the property key of the translation.
     * This one might not be unique in a locale.
     */
    public function getKey(): string
    {
        return $this->key;
    }


    public function getValue(): string
    {
        return $this->value;
    }


    public function getGroup(): string
    {
        return $this->group;
    }


    public function setValue(string $newValue): void
    {
        $this->value = $newValue;
    }


    public function isEmpty(): bool
    {
        return (trim($this->value) === '');
    }


    public function getLevel(string $delimiter): int
    {
        if ($delimiter === '') {
            return 0;
        }

        if (!$this->stringDoesContain($this->key, $delimiter)) {
            return 0;
        }

        $parts = explode($delimiter, $this->key);

        return count($parts) - 1;
    }
}
