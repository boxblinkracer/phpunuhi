<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class SnippetSet
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $iso;

    /**
     * @param string $id
     * @param string $name
     * @param string $iso
     */
    public function __construct(string $id, string $name, string $iso)
    {
        $this->id = $id;
        $this->name = $name;
        $this->iso = $iso;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getIso(): string
    {
        return $this->iso;
    }

}
