<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Reporter\Model;

class ReportSetResult
{
    private string $name;

    /**
     * @var ReportTestResult[]
     */
    private array $tests = [];


    public function __construct(string $name)
    {
        $this->name = $name;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function addTestResult(ReportTestResult $result): void
    {
        $this->tests[] = $result;
    }

    /**
     * @return ReportTestResult[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }


    public function getTestCount(): int
    {
        return count($this->tests);
    }


    public function getFailureCount(): int
    {
        $count = 0;
        foreach ($this->tests as $test) {
            if (!$test->isSuccess()) {
                $count++;
            }
        }

        return $count;
    }
}
