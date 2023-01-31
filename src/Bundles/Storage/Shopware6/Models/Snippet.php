<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class Snippet
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $snippetSetId;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $customFields;


    /**
     * @param string $id
     * @param string $snippetSetId
     * @param string $key
     * @param string $value
     * @param string $author
     * @param string $customFields
     */
    public function __construct(string $id, string $snippetSetId, string $key, string $value, string $author, string $customFields)
    {
        $this->id = $id;
        $this->snippetSetId = $snippetSetId;
        $this->key = $key;
        $this->value = $value;
        $this->author = $author;
        $this->customFields = $customFields;
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
    public function getSnippetSetId(): string
    {
        return $this->snippetSetId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getCustomFields(): string
    {
        return $this->customFields;
    }

}
