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
     * @var bool
     */
    private $success;


    /**
     * @param string $locale
     * @param string $title
     * @param bool $success
     */
    public function __construct(string $locale, string $title, bool $success)
    {
        $this->locale = $locale;
        $this->title = $title;
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
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}