<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class Snippet
{
    private string $id;

    private string $snippetSetId;

    private string $key;

    private string $value;

    private string $author;

    private string $customFields;



    public function __construct(string $id, string $snippetSetId, string $key, string $value, string $author, string $customFields)
    {
        $this->id = $id;
        $this->snippetSetId = $snippetSetId;
        $this->key = $key;
        $this->value = $value;
        $this->author = $author;
        $this->customFields = $customFields;
    }


    public function getId(): string
    {
        return $this->id;
    }


    public function getSnippetSetId(): string
    {
        return $this->snippetSetId;
    }


    public function getKey(): string
    {
        return $this->key;
    }


    public function getValue(): string
    {
        return $this->value;
    }


    public function getAuthor(): string
    {
        return $this->author;
    }


    public function getCustomFields(): string
    {
        return $this->customFields;
    }
}
