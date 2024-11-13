<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Spelling\Result;

class SpellingValidationResult
{
    private bool $isValid;

    private string $locale;

    private string $suggestedText;

    /**
     * @var MisspelledWord[]
     */
    private array $misspelledWords;


    /**
     * @param MisspelledWord[] $misspelledWords
     */
    public function __construct(bool $isValid, string $locale, string $suggestedText, array $misspelledWords)
    {
        $this->isValid = $isValid;
        $this->locale = $locale;
        $this->suggestedText = $suggestedText;
        $this->misspelledWords = $misspelledWords;
    }


    public function isValid(): bool
    {
        return $this->isValid;
    }


    public function getLocale(): string
    {
        return $this->locale;
    }


    public function getSuggestedText(): string
    {
        return $this->suggestedText;
    }

    /**
     * @return MisspelledWord[]
     */
    public function getMisspelledWords(): array
    {
        return $this->misspelledWords;
    }
}
