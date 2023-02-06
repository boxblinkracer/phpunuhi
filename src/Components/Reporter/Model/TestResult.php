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
     * @var string
     */
    private $failureType;

    /**
     * @var bool
     */
    private $success;


    /**
     * @param string $name
     * @param string $className
     * @param string $failureType
     * @param bool $success
     */
    public function __construct(string $name, string $className, string $failureType, bool $success)
    {
        $this->name = $name;
        $this->className = $className;
        $this->failureType = $failureType;
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
     * @return string
     */
    public function getFailureType(): string
    {
        return $this->failureType;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}