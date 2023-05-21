<?php

namespace PHPUnuhi\Services\Coverage\Models;

use PHPUnuhi\Services\Coverage\Traits\CoverageDataTrait;

class CoverageTotal
{

    use CoverageDataTrait;

    /**
     * @var CoverageSet[]
     */
    private $coverageSets;


    /**
     * @param CoverageSet[] $coverageSets
     */
    public function __construct(array $coverageSets)
    {
        $this->coverageSets = $coverageSets;

        $this->calculate();
    }

    /**
     * @return CoverageSet[]
     */
    public function getCoverageSets(): array
    {
        return $this->coverageSets;
    }

    /**
     * @return void
     */
    private function calculate(): void
    {
        $this->countTranslated = 0;
        $this->countAll = 0;
        $this->countWords = 0;

        foreach ($this->coverageSets as $coverage) {

            $this->countTranslated += $coverage->getCountTranslated();
            $this->countAll += $coverage->getCountAll();
            $this->countWords += $coverage->getWordCount();
        }
    }

}

