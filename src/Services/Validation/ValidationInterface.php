<?php

namespace PHPUnuhi\Services\Validation;
interface ValidationInterface
{

    /**
     * @param array<string> $files
     * @return bool
     */
    function validate(array $files): bool;

}