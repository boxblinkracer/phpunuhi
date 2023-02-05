<?php

namespace PHPUnuhi\Components\Validator\Model;

class ValidationResult
{

    /**
     * @var ValidationTest[]
     */
    private $tests;


    /**
     * @var ValidationError[]
     */
    private $errors;

    
    /**
     * @param ValidationTest[] $tests
     * @param ValidationError[] $errors
     */
    public function __construct(array $tests, array $errors)
    {
        $this->tests = $tests;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * @return ValidationTest[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}
