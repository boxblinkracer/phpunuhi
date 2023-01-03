<?php

namespace PHPUnuhi\Models\Configuration;

class TranslationSuite
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string>
     */
    private $files;


    /**
     * @param string $name
     * @param string[] $files
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
     * @return string[]
     */
    public function getFiles()
    {
        return $this->files;
    }

}
