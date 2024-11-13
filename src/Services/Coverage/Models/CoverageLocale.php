<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Coverage\Models;

use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Coverage\Traits\CoverageDataTrait;
use PHPUnuhi\Services\WordCounter\WordCounter;

class CoverageLocale
{
    use CoverageDataTrait;

    private Locale $locale;



    public function __construct(Locale $locale)
    {
        $this->locale = $locale;

        $this->calculate();
    }



    public function getLocaleName(): string
    {
        return $this->locale->getName();
    }




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
