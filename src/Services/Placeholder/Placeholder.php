<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Placeholder;

class Placeholder
{
    private string $id;

    private string $value;



    public function __construct(string $value)
    {
        $this->id = md5($value);
        $this->value = $value;
    }


    public function getId(): string
    {
        return $this->id;
    }


    public function getValue(): string
    {
        return $this->value;
    }
}
