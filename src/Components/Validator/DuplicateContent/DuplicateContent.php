<?php

namespace PHPUnuhi\Components\Validator\DuplicateContent;

class DuplicateContent
{

    /**
     * @var string
     */
    private $locale;

    /**
     * @var bool
     */
    private $duplicateAllowed;

    /**
     * @param string $locale
     * @param bool $duplicateAllowed
     */
    public function __construct(string $locale, bool $duplicateAllowed)
    {
        $this->locale = $locale;
        $this->duplicateAllowed = $duplicateAllowed;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return bool
     */
    public function isDuplicateAllowed(): bool
    {
        return $this->duplicateAllowed;
    }
}
