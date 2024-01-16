<?php

namespace PHPUnuhi\Components\Reporter\Model;

class ReportResult
{

    /**
     * @var TranslationSetResult[]
     */
    private $suites = [];


    /**
     * @param TranslationSetResult $result
     * @return void
     */
    public function addTranslationSet(TranslationSetResult $result): void
    {
        $this->suites[] = $result;
    }

    /**
     * @return TranslationSetResult[]
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
