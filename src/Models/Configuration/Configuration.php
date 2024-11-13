<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

use PHPUnuhi\Models\Translation\TranslationSet;

class Configuration
{
    /**
     * @var array<TranslationSet>
     */
    private array $sets;

    private Coverage $coverage;


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


    public function getCoverage(): Coverage
    {
        return $this->coverage;
    }


    public function setCoverage(Coverage $coverage): void
    {
        $this->coverage = $coverage;
    }


    public function hasCoverageSetting(): bool
    {
        if ($this->coverage->hasMinCoverage()) {
            return true;
        }

        if ($this->coverage->getLocaleCoverages() !== []) {
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
