<?php

namespace PHPUnuhi\Components\Reporter\Model;

class TranslationSetResult
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var TestResult[]
     */
    private $tests = [];

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param TestResult $result
     * @return void
     */
    public function addTestResult(TestResult $result): void
    {
        $this->tests[] = $result;
    }

    /**
     * @return TestResult[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @return int
     */
    public function getTestCount(): int
    {
        return count($this->tests);
    }

    /**
     * @return int
     */
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
