<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Model;

use PHPUnuhi\Models\Translation\Locale;

class ValidationTest
{
    private string $translationKey;

    private ?Locale $locale;

    private string $title;

    private string $filename;

    private int $lineNumber;

    private string $classification;

    private string $failureMessage;

    private bool $success;


    public function __construct(string $translationKey, ?Locale $locale, string $title, string $filename, int $lineNumber, string $classification, string $failureMessage, bool $success)
    {
        $this->translationKey = $translationKey;
        $this->locale = $locale;
        $this->title = $title;
        $this->filename = $filename;
        $this->lineNumber = $lineNumber;
        $this->classification = $classification;
        $this->failureMessage = $failureMessage;
        $this->success = $success;
    }


    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }


    public function getTitle(): string
    {
        $localeName = $this->locale instanceof Locale ? $this->locale->getName() : '-';

        return '[' . $localeName . '] ' . $this->title;
    }


    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }


    public function getClassification(): string
    {
        return $this->classification;
    }


    public function getFailureMessage(): string
    {
        return $this->failureMessage;
    }


    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }
}
