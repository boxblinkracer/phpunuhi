<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\Model;

class ReportTestResult
{
    private string $name;

    private string $translationKey;

    private string $className;

    private int $lineNumber;

    private string $failureType;

    private string $failureMessage;

    private bool $success;



    public function __construct(
        string $name,
        string $translationKey,
        string $className,
        int $lineNumber,
        string $failureType,
        string $failureMessage,
        bool $success
    ) {
        $this->name = $name;
        $this->translationKey = $translationKey;
        $this->className = $className;
        $this->lineNumber = $lineNumber;
        $this->failureType = $failureType;
        $this->failureMessage = $failureMessage;
        $this->success = $success;
    }



    public function getName(): string
    {
        return $this->name;
    }


    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }


    public function getClassName(): string
    {
        return $this->className;
    }


    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }


    public function getFailureType(): string
    {
        return $this->failureType;
    }


    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }


    public function isSuccess(): bool
    {
        return $this->success;
    }
}
