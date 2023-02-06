<?php

namespace PHPUnuhi\Components\Reporter\Model;

class TestResult
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $success;


    /**
     * @param string $name
     * @param string $className
     * @param bool $success
     */
    public function __construct(string $name, string $className, bool $success)
    {
        $this->name = $name;
        $this->className = $className;
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
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}