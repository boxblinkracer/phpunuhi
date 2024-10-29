<?php

namespace PHPUnuhi\Models\Text;

class Text
{

    /**
     * @var string
     */
    private $originalText;

    /**
     * @var string
     */
    private $encodedText;

    /**
     * @param string $originalText
     * @param string $encodedText
     */
    public function __construct(string $originalText, string $encodedText)
    {
        $this->originalText = $originalText;
        $this->encodedText = $encodedText;
    }

    public function getOriginalText(): string
    {
        return $this->originalText;
    }

    public function getEncodedText(): string
    {
        return $this->encodedText;
    }
}
