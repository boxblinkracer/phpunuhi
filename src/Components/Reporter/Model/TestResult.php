<?php

namespace PHPUnuhi\Components\Reporter\Model;

class TestResult
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $success;


    /**
     * @param string $name
     * @param bool $success
     */
    public function __construct(string $name, bool $success)
    {
        $this->name = $name;
        $this->success = $success;
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
    public function isSuccess(): bool
    {
        return $this->success;
    }

}