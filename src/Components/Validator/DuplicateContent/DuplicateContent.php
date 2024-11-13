<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\DuplicateContent;

class DuplicateContent
{
    private string $locale;

    private bool $duplicateAllowed;


    public function __construct(string $locale, bool $duplicateAllowed)
    {
        $this->locale = $locale;
        $this->duplicateAllowed = $duplicateAllowed;
    }


    public function getLocale(): string
    {
        return $this->locale;
    }


    public function isDuplicateAllowed(): bool
    {
        return $this->duplicateAllowed;
    }
}
