<?php

namespace PHPUnuhi\Models\Configuration;


class Configuration
{

    /**
     * @var array<TranslationSuite>
     */
    private $translationSuites;

    /**
     * @param TranslationSuite[] $translationSuites
     */
    public function __construct(array $translationSuites)
    {
        $this->translationSuites = $translationSuites;
    }

    /**
     * @return TranslationSuite[]
     */
    public function getTranslationSuites()
    {
        return $this->translationSuites;
    }

}
