<?php

namespace PHPUnuhi\Components\Reporter\Model;

class ReportResult
{

    /**
     * @var ReportSetResult[]
     */
    private $suites = [];


    /**
     * @param ReportSetResult $result
     * @return void
     */
    public function addTranslationSet(ReportSetResult $result): void
    {
        $this->suites[] = $result;
    }

    /**
     * @return ReportSetResult[]
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
