<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\Model;

class ReportResult
{
    /**
     * @var ReportSetResult[]
     */
    private array $suites = [];



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


    public function getTestCount(): int
    {
        $count = 0;
        foreach ($this->suites as $suite) {
            $count += $suite->getTestCount();
        }

        return $count;
    }


    public function getFailureCount(): int
    {
        $count = 0;
        foreach ($this->suites as $suite) {
            $count += $suite->getFailureCount();
        }

        return $count;
    }
}
