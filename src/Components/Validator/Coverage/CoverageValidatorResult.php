<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Coverage;

class CoverageValidatorResult
{
    private bool $success;

    private float $coverageExpected;

    private float $coverageActual;

    private string $scope;



    public function __construct(bool $success, float $coverageExpected, float $coverageActual, string $scope)
    {
        $this->success = $success;
        $this->coverageExpected = $coverageExpected;
        $this->coverageActual = $coverageActual;
        $this->scope = $scope;
    }


    public function isSuccess(): bool
    {
        return $this->success;
    }


    public function getCoverageExpected(): float
    {
        return $this->coverageExpected;
    }


    public function getCoverageActual(): float
    {
        return $this->coverageActual;
    }


    public function getScope(): string
    {
        return $this->scope;
    }
}
