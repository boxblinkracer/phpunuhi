<?php

namespace PHPUnuhi\Components\Validator\Model;

class ValidationError
{

    /**
     * @var string
     */
    private $classification;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $lineNumber;


    /**
     * @param string $classification
     * @param string $message
     * @param string $locale
     * @param string $filename
     * @param string $identifier
     * @param int $lineNumber
     */
    public function __construct(
        string $classification,
        string $message,
        string $locale,
        string $filename,
        string $identifier,
        int $lineNumber
    ) {
        $this->classification = $classification;
        $this->message = $message;
        $this->locale = $locale;
        $this->filename = $filename;
        $this->identifier = $identifier;
        $this->lineNumber = $lineNumber;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
