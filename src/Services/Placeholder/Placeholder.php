<?php

namespace PHPUnuhi\Services\Placeholder;

class Placeholder
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $value;


    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->id = md5($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
