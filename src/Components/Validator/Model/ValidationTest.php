<?php

namespace PHPUnuhi\Components\Validator\Model;

class ValidationTest
{

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
     * @var bool
     */
    private $success;


    /**
     * @param string $locale
     * @param string $title
     * @param string $filename
     * @param bool $success
     */
    public function __construct(string $locale, string $title, string $filename, bool $success)
    {
        $this->locale = $locale;
        $this->title = $title;
        $this->filename = $filename;
        $this->success = $success;
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

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}