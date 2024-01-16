<?php

namespace PHPUnuhi\Components\Validator\Coverage;

class CoverageValidatorResult
{

    /**
     * @var bool
     */
    private $success;

    /**
     * @var float
     */
    private $coverageExpected;

    /**
     * @var float
     */
    private $coverageActual;

    /**
     * @var string
     */
    private $scope = '';


    /**
     * @param bool $success
     * @param float $coverageExpected
     * @param float $coverageActual
     * @param string $scope
     */
    public function __construct(bool $success, float $coverageExpected, float $coverageActual, string $scope)
    {
        $this->success = $success;
        $this->coverageExpected = $coverageExpected;
        $this->coverageActual = $coverageActual;
        $this->scope = $scope;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return float
     */
    public function getCoverageExpected(): float
    {
        return $this->coverageExpected;
    }

    /**
     * @return float
     */
    public function getCoverageActual(): float
    {
        return $this->coverageActual;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }
}
