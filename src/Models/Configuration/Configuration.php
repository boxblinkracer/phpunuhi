<?php

namespace PHPUnuhi\Models\Configuration;


use PHPUnuhi\Models\Translation\TranslationSet;

class Configuration
{

    /**
     * @var array<TranslationSet>
     */
    private $translationSuites;

    /**
     * @param TranslationSet[] $translationSuites
     */
    public function __construct(array $translationSuites)
    {
        $this->translationSuites = $translationSuites;
    }

    /**
     * @return TranslationSet[]
     */
    public function getTranslationSets()
    {
        return $this->translationSuites;
    }

}
