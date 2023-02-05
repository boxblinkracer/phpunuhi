<?php

namespace PHPUnuhi\Components\Validator\Model;

class ValidationResult
{

    /**
     * @var ValidationError[]
     */
    private $errors;

    /**
     * @param ValidationError[] $errors
     */
    public function __construct(array $errors)
    {
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
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}
