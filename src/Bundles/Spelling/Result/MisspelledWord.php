<?php

namespace PHPUnuhi\Bundles\Spelling\Result;

class MisspelledWord
{

    /**
     * @var string
     */
    private $word;

    /**
     * @var string[]
     */
    private $suggestions;

    /**
     * @param string $word
     * @param string[] $suggestions
     */
    public function __construct(string $word, array $suggestions)
    {
        $this->word = $word;
        $this->suggestions = $suggestions;
    }

    /**
     * @return string
     */
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
