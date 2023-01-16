<?php

namespace PHPUnuhi\Models\Configuration;


use PHPUnuhi\Models\Translation\TranslationSet;

class Configuration
{

    /**
     * @var array<TranslationSet>
     */
    private $sets;

    /**
     * @param TranslationSet[] $sets
     */
    public function __construct(array $sets)
    {
        $this->sets = $sets;
    }

    /**
     * @return TranslationSet[]
     */
    public function getTranslationSets()
    {
        return $this->sets;
    }

}
