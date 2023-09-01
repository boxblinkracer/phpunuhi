<?php

namespace PHPUnuhi\Components\Validator\Model;

class ValidationTest
{

    /**
     * @var string
     */
    private $translationKey;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var int
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $classification;

    /**
     * @var string
     */
    private $failureMessage;

    /**
     * @var bool
     */
    private $success;


    /**
     * @param string $translationKey
     * @param string $locale
     * @param string $title
     * @param string $filename
     * @param string $classification
     * @param string $failureMessage
     * @param bool $success
     */
    public function __construct(
        string $translationKey,
        string $locale,
        string $title,
        string $filename,
        int $lineNumber,
        string $classification,
        string $failureMessage,
        bool $success
    ) {
        $this->translationKey = $translationKey;
        $this->locale = $locale;
        $this->title = $title;
        $this->filename = $filename;
        $this->lineNumber = $lineNumber;
        $this->classification = $classification;
        $this->failureMessage = $failureMessage;
        $this->success = $success;
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
    public function getTitle(): string
    {
        return '[' . $this->locale . '] ' . $this->title;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @return string
     */
    public function getClassification(): string
    {
        return $this->classification;
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