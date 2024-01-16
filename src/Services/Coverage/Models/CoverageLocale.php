<?php

namespace PHPUnuhi\Services\Coverage\Models;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Coverage\Traits\CoverageDataTrait;
use PHPUnuhi\Services\WordCounter\WordCounter;

class CoverageLocale
{
    use CoverageDataTrait;

    /**
     * @var Locale
     */
    private $locale;


    /**
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;

        $this->calculate();
    }


    /**
     * @return string
     */
    public function getLocaleName(): string
    {
        return $this->locale->getName();
    }



    /**
     * @return void
     */
    private function calculate(): void
    {
        $wordCounter = new WordCounter();

        $this->countTranslated = count($this->locale->getValidTranslations());
        $this->countAll = count($this->locale->getTranslationIDs());

        $this->countWords = 0;
        foreach ($this->locale->getValidTranslations() as $translation) {
            $this->countWords += $wordCounter->getWordCount($translation->getValue());
        }
    }
}
