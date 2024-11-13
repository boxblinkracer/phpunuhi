<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle;

interface CaseStyleValidatorInterface
{
    public function getIdentifier(): string;


    public function isValid(string $text): bool;
}
