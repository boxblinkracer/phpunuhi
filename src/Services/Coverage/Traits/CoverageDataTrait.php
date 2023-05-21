<?php

namespace PHPUnuhi\Services\Coverage\Traits;

use PHPUnuhi\Services\Maths\PercentageCalculator;

trait CoverageDataTrait
{

    /**
     * @var int
     */
    protected $countAll;

    /**
     * @var int
     */
    protected $countTranslated;

    /**
     * @var int
     */
    protected $countWords;


    /**
     * @return float
     */
    public function getCoverage(): float
    {
        $calculator = new PercentageCalculator();

        return $calculator->getRoundedPercentage($this->countTranslated, $this->countAll);
    }

    /**
     * @return int
     */
    public function getCountTranslated(): int
    {
        return $this->countTranslated;
    }

    /**
     * @return int
     */
    public function getCountAll(): int
    {
        return $this->countAll;
    }

    /**
     * @return int
     */
    public function getWordCount(): int
    {
        return $this->countWords;
    }

}
