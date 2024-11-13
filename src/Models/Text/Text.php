<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Text;

class Text
{
    private string $originalText;

    private string $encodedText;


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
