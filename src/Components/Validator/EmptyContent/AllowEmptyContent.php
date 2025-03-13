<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\EmptyContent;

class AllowEmptyContent
{
    private string $key;

    /**
     * @var list<string>
     */
    private array $locales;


    /**
     * @param list<string> $locales
     */
    public function __construct(string $key, array $locales)
    {
        $this->key = $key;
        $this->locales = $locales;
    }


    public function getKey(): string
    {
        return $this->key;
    }


    public function isLocaleAllowed(string $locale): bool
    {
        # if we have an entry without locales, we allow all locales to be empty
        if ($this->locales === []) {
            return true;
        }

        # if we have a locale with * wildcard, we also allow all locales to be empty
        if (in_array('*', $this->locales, true)) {
            return true;
        }

        return in_array($locale, $this->locales, true);
    }
}
