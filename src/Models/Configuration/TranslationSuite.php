<?php

namespace PHPUnuhi\Models\Configuration;

class TranslationSuite
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<mixed>
     */
    private $files;


    /**
     * @param string $name
     * @param array<mixed> $files
     */
    public function __construct($name, array $files)
    {
        $this->name = $name;
        $this->files = $files;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array<mixed>
     */
    public function getFiles()
    {
        return $this->files;
    }

}
