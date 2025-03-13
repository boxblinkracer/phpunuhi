<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Coverage\Traits;

use PHPUnuhi\Services\Maths\PercentageCalculator;

trait CoverageDataTrait
{
    /**
     * @var int
     */
    protected int $countAll;

    /**
     * @var int
     */
    protected int $countTranslated;

    /**
     * @var int
     */
    protected int $countWords;



    public function getCoverage(): float
    {
        return (new PercentageCalculator())->getRoundedPercentage($this->countTranslated, $this->countAll);
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
