<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class SnippetSet
{
    private string $id;

    private string $name;

    private string $iso;


    public function __construct(string $id, string $name, string $iso)
    {
        $this->id = $id;
        $this->name = $name;
        $this->iso = $iso;
    }


    public function getId(): string
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getIso(): string
    {
        return $this->iso;
    }
}
