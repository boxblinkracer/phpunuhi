<?php

namespace PHPUnuhi\Components\Validator\EmptyContent;

class AllowEmptyContent
{

    /**
     * @var string
     */
    private $key;

    /**
     * @var array<mixed>
     */
    private $locales;


    /**
     * @param string $key
     * @param mixed[] $locales
     */
    public function __construct(string $key, array $locales)
    {
        $this->key = $key;
        $this->locales = $locales;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $locale
     * @return bool
     */
    public function isLocaleAllowed(string $locale) : bool
    {
        return in_array($locale, $this->locales);
    }
}
