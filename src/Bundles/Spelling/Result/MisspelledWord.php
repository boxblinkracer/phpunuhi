<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Spelling\Result;

class MisspelledWord
{
    private string $word;

    /**
     * @var string[]
     */
    private array $suggestions;

    /**
     * @param string[] $suggestions
     */
    public function __construct(string $word, array $suggestions)
    {
        $this->word = $word;
        $this->suggestions = $suggestions;
    }


    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }
}
