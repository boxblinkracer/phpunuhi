<?php

namespace PHPUnuhi\Bundles\Spelling\Result;

class SpellingValidationResult
{

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $suggestedText;

    /**
     * @var MisspelledWord[]
     */
    private $misspelledWords;


    /**
     * @param bool $isValid
     * @param string $locale
     * @param MisspelledWord[] $misspelledWords
     */
    public function __construct(bool $isValid, string $locale, string $suggestedText, array $misspelledWords)
    {
        $this->isValid = $isValid;
        $this->locale = $locale;
        $this->suggestedText = $suggestedText;
        $this->misspelledWords = $misspelledWords;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
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
