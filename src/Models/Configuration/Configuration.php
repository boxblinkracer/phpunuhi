<?php

namespace PHPUnuhi\Models\Configuration;

use PHPUnuhi\Models\Configuration\Coverage\Coverage;
use PHPUnuhi\Models\Translation\TranslationSet;

class Configuration
{

    /**
     * @var array<TranslationSet>
     */
    private $sets;

    /**
     * @var Coverage
     */
    private $coverage;


    /**
     * @param TranslationSet[] $sets
     */
    public function __construct(array $sets)
    {
        $this->sets = $sets;

        $this->coverage = new Coverage();
    }

    /**
     * @return TranslationSet[]
     */
    public function getTranslationSets()
    {
        return $this->sets;
    }

    /**
     * @return Coverage
     */
    public function getCoverage(): Coverage
    {
        return $this->coverage;
    }

    /**
     * @param Coverage $coverage
     * @return void
     */
    public function setCoverage(Coverage $coverage): void
    {
        $this->coverage = $coverage;
    }

    /**
     * @return bool
     */
    public function hasCoverageSetting(): bool
    {
        if ($this->coverage->hasTotalMinCoverage()) {
            return true;
        }

        if (count($this->coverage->getLocaleCoverages()) > 0) {
            return true;
        }

        foreach ($this->sets as $set) {
            if ($set->getCoverage()->hasTotalMinCoverage()) {
                return true;
            }

            if (count($set->getCoverage()->getLocaleCoverages()) > 0) {
                return true;
            }
        }

        return false;
    }
}
