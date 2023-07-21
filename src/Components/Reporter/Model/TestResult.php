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
    private $translationKey;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $failureType;

    /**
     * @var string
     */
    private $failureMessage;

    /**
     * @var bool
     */
    private $success;


    /**
     * @param string $name
     * @param string $translationKey
     * @param string $className
     * @param string $failureType
     * @param string $failureMessage
     * @param bool $success
     */
    public function __construct(string $name, string $translationKey, string $className, string $failureType, string $failureMessage, bool $success)
    {
        $this->name = $name;
        $this->translationKey = $translationKey;
        $this->className = $className;
        $this->failureType = $failureType;
        $this->failureMessage = $failureMessage;
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
    public function getTranslationKey(): string
    {
        return $this->translationKey;
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
     * @return string
     */
    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}