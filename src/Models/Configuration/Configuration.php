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
    public function getTranslationSets(): array
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
        if ($this->coverage->hasMinCoverage()) {
            return true;
        }

        if (count($this->coverage->getLocaleCoverages()) > 0) {
            return true;
        }

        foreach ($this->sets as $set) {
            if (!$this->coverage->hasTranslationSetCoverage($set->getName())) {
                continue;
            }

            $setCoverage = $this->coverage->getTranslationSetCoverage($set->getName());

            if ($setCoverage->hasMinCoverage()) {
                return true;
            }

            if ($setCoverage->hasLocaleCoverages()) {
                return true;
            }
        }

        return false;
    }
}
