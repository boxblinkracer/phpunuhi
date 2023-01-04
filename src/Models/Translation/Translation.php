<?php

namespace PHPUnuhi\Models\Translation;

class Translation
{

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $locale
     * @param string $key
     * @param string $value
     */
    public function __construct(string $locale, string $key, string $value)
    {
        $this->locale = $locale;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
