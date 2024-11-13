<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\CaseStyle\Style;

use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;

/**
 * This validator can be used to explicitly disable case style validation in
 * specific levels, while other levels (or the whole files) only allow specific styles.
 */
class NoneCaseValidator implements CaseStyleValidatorInterface
{
    public function getIdentifier(): string
    {
        return 'none';
    }


    public function isValid(string $text): bool
    {
        return true;
    }
}
