<?php

namespace PHPUnuhi\Models\Command;

class ErrorTableRow
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $classType;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $line;

    /**
     * @param string $id
     * @param string $classType
     * @param string $locale
     * @param string $key
     * @param string $error
     * @param string $file
     * @param string $line
     */
    public function __construct(string $id, string $classType, string $locale, string $key, string $error, string $file, string $line)
    {
        $this->id = $id;
        $this->classType = $classType;
        $this->locale = $locale;
        $this->error = $error;
        $this->key = $key;
        $this->file = $file;
        $this->line = $line;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClassType(): string
    {
        return $this->classType;
    }



    public function getError(): string
    {
        return $this->error;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
