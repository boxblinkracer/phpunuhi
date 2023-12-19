<?php

namespace PHPUnuhi\Components\Reporter\Model;

class ReportResult
{

    /**
     * @var SuiteResult[]
     */
    private $suites = [];


    /**
     *
     */
    public function __construct()
    {
    }


    /**
     * @param SuiteResult $result
     * @return void
     */
    public function addSuite(SuiteResult $result): void
    {
        $this->suites[] = $result;
    }

    /**
     * @return SuiteResult[]
     */
    public function getSuites(): array
    {
        return $this->suites;
    }

    /**
     * @return int
     */
    public function getTestCount(): int
    {
        $count = 0;
        foreach ($this->suites as $suite) {
            $count += $suite->getTestCount();
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getFailureCount(): int
    {
        $count = 0;
        foreach ($this->suites as $suite) {
            $count += $suite->getFailureCount();
        }

        return $count;
    }

}