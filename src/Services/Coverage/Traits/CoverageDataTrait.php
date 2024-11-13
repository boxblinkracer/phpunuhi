<?php

declare(strict_types=1);

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



    public function getCoverage(): float
    {
        $calculator = new PercentageCalculator();

        return $calculator->getRoundedPercentage($this->countTranslated, $this->countAll);
    }


    public function getCountTranslated(): int
    {
        return $this->countTranslated;
    }


    public function getCountAll(): int
    {
        return $this->countAll;
    }


    public function getWordCount(): int
    {
        return $this->countWords;
    }
}
