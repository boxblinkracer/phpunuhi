<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Model;

class ValidationResult
{
    /**
     * @var ValidationTest[]
     */
    private array $tests;


    /**
     * @param ValidationTest[] $tests
     */
    public function __construct(array $tests)
    {
        $this->tests = $tests;
    }


    public function isValid(): bool
    {
        return $this->getErrors() === [];
    }

    /**
     * @return ValidationTest[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @return ValidationTest[]
     */
    public function getErrors(): array
    {
        $errors = [];

        foreach ($this->tests as $test) {
            if (!$test->isSuccess()) {
                $errors[] = $test;
            }
        }

        return $errors;
    }
}
